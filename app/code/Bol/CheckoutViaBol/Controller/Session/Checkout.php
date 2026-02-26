<?php

namespace Bol\CheckoutViaBol\Controller\Session;

use Bol\CheckoutViaBol\Model\Cvb\CvbSession;
use Bol\CheckoutViaBol\Model\Logger;
use Bol\CheckoutViaBol\Model\Ui\ConfigProvider;
use Bol\CheckoutViaBol\Service\ConfigService;
use Bol\CheckoutViaBol\Service\CvbApi;
use Bol\CheckoutViaBol\Service\CvbAvailabilityService;
use Bol\CheckoutViaBol\Service\RedirectService;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Model\Quote;

class Checkout implements HttpGetActionInterface
{
    public function __construct(
        private readonly Context                          $context,
        private readonly Logger                           $logger,
        private readonly CvbApi                           $cvbApi,
        private readonly CheckoutSession                  $magentoCheckoutSession,
        private readonly CartRepositoryInterface          $cartRepository,
        private readonly ConfigService                    $configService,
        private readonly RedirectService                  $redirectService,
        private readonly PaymentMethodManagementInterface $paymentMethodManagement,
        private readonly CvbAvailabilityService           $cvbAvailabilityService
    ) {
    }

    public function execute(): ResultInterface
    {
        $cvbSessionData = CvbSession::fromRequest($this->context->getRequest());
        if (!$cvbSessionData->success) {
            $this->logger->error('Invalid session data id: {id}', ['id' => $cvbSessionData->sid]);
            return $this->redirectService->redirectToCart();
        }

        $originalCartId = $this->context->getRequest()->getParam('cartId');

        $quote = $this->resolveQuote($originalCartId);
        if (!$quote) {
            $this->context->getMessageManager()
                ->addErrorMessage(
                    'Could not find your shopping cart. Are you on the same browser? Please refill the shopping cart and try checkout via bol again.'
                );
            return $this->redirectService->redirectToCart();
        }

        $cvbSession = $this->cvbApi->getCvbSession(
            $cvbSessionData->sid,
            $cvbSessionData->nonce
        );

        $bnplSelected = $this->isBnplSelected($cvbSession);

        # Set the cvbSession on the magento checkout session object. We will use this later on when converting
        # to a cvb order.
        /** @see \Magento\Framework\Session\SessionManager::__call for how ->setCvbSession is handled */
        /** @noinspection PhpUndefinedMethodInspection */
        $this->magentoCheckoutSession->setCvbSessionId($cvbSessionData->sid);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->magentoCheckoutSession->setIsBnplSelected($bnplSelected);
        $this->magentoCheckoutSession->setHideCvbButton(true);
        $this->preFillQuote($cvbSession, $quote);

        return $this->redirectToCheckout();
    }

    private function preFillQuote(array $cvbSession, Quote $quote): void
    {
        $cvbBillingAddress  = $cvbSession['choices']['billingAddress'] ?? [];
        $cvbShippingAddress = $cvbSession['choices']['shippingAddress'] ?? [];

        $areBillingAndShippingTheSame = $cvbShippingAddress === $cvbBillingAddress;

        $email = $cvbSession['personDetails']['email'] ?? '';
        $phone = $cvbSession['personDetails']['phoneNumber'] ?? '';

        $quote->setCustomerEmail($email);
        # Set session id (for persistence) and update address fields
        $quote->getBillingAddress()
            ->setEmail($email)
            ->setFirstname($cvbBillingAddress['firstName'] ?? '')
            ->setLastname($this->resolveLastName($cvbBillingAddress))
            ->setCity($cvbBillingAddress['city'] ?? '')
            ->setPostcode($cvbBillingAddress['postalCode'] ?? '')
            ->setCountryId($cvbBillingAddress['countryCode'] ?? '')
            ->setCompany($cvbBillingAddress['companyName'] ?? '')
            ->setStreet($this->resolveStreetArray($cvbBillingAddress))
            ->setTelephone($phone);

        $quote->getShippingAddress()->setSameAsBilling($areBillingAndShippingTheSame)
            ->setEmail($email)
            ->setFirstname($cvbShippingAddress['firstName'] ?? '')
            ->setLastname($this->resolveLastName($cvbShippingAddress))
            ->setCity($cvbShippingAddress['city'] ?? '')
            ->setPostcode($cvbShippingAddress['postalCode'] ?? '')
            ->setCountryId($cvbShippingAddress['countryCode'] ?? '')
            ->setCompany($cvbShippingAddress['companyName'] ?? '')
            ->setStreet($this->resolveStreetArray($cvbShippingAddress))
            ->setTelephone($phone);

        if ($this->isBnplSelected($cvbSession) && $this->cvbAvailabilityService->isCvbAvailable()) {
            $cvbMethod = array_filter(
                $this->paymentMethodManagement->getList($quote->getId()),
                static fn (MethodInterface $method) => $method->getCode() === ConfigProvider::CODE
            );
            $cvbMethod = reset($cvbMethod);

            if ($cvbMethod) {
                $quote->getPayment()->setMethod(ConfigProvider::CODE);
            }
        }

        $this->cartRepository->save($quote);
    }

    private function resolveLastName(array $cvbAddress): string
    {
        $infix    = $cvbAddress['infix'] ?? '';
        $lastname = $cvbAddress['lastName'] ?? '';

        return trim(sprintf('%s %s', trim($infix), $lastname));
    }

    /**
     * @param array $cvbSession
     *
     * @return bool
     */
    private function isBnplSelected(array $cvbSession): bool
    {
        return $cvbSession['choices']['bnpl'] ?? false;
    }

    /**
     * Magento 'streets' are an array containing 'lines' the default is 2 lines
     * In the case of 2 lines it is assumed that the street name is on line 1 and
     * the house number plus possible additions are on line 2
     *
     * In the case of 1 line everything is on that line
     * In the case of 3 lines then line 1 is street name, line 2 is house number and line 3 is house number addition
     * For example
     * [
     *      'test street',
     *      '1',
     *      'A'
     * ]
     *
     * We should verify if this implementation has implications for address validation/autocomplete checks
     *
     * @param array $cvbAddress
     *
     * @return array
     */
    private function resolveStreetArray(array $cvbAddress): array
    {
        $streetName              = $cvbAddress['streetName'] ?? '';
        $houseNumber             = $cvbAddress['houseNumber'] ?? '';
        $houseNumberExtension    = $cvbAddress['houseNumberExtension'] ?? '';
        $magentoStreetLinesCount = $this->configService->getAddressStreetLineCount();

        return match ($magentoStreetLinesCount) {
            1 => [
                sprintf('%s %s %s', $streetName, $houseNumber, $houseNumberExtension)
            ],
            2 => [
                $streetName,
                sprintf('%s %s', $houseNumber, $houseNumberExtension)
            ],
            default => [$streetName, $houseNumber, $houseNumberExtension]
        };
    }

    /**
     * @return ResultInterface
     */
    private function redirectToCheckout(): ResultInterface
    {
        return $this->context->getResultRedirectFactory()->create()->setUrl(
            $this->context->getUrl()->getUrl(
                'checkout',
                ['_query' => ['cvb_ar' => 1]]
            )
        );
    }

    private function resolveQuote(string $cartId): ?Quote
    {
        $currentQuote = $this->magentoCheckoutSession->getQuote();

        if ((string)$currentQuote->getId() === $cartId) {
            return $currentQuote;
        }

        try {
            // Only try to get active quotes.
            /** @var Quote $cvbQuote */
            $cvbQuote = $this->cartRepository->getActive($cartId);
        } catch (NoSuchEntityException $e) {
            return null;
        }

        if ($cvbQuote->getCustomer()->getId() !== $currentQuote->getCustomer()->getId()) {
            // If the quote used to create the cvb session was in another browser where the customer was logged in
            // and the customer is not logged in the current browser then this case triggers.
            return null;
        }

        // Purge the current quote and replace it with the quote posted to CVB.
        $this->magentoCheckoutSession->clearQuote();
        $this->magentoCheckoutSession->setQuoteId((int)$cartId);
        return $this->magentoCheckoutSession->getQuote();
    }
}
