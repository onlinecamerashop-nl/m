<?php

namespace Bol\CheckoutViaBol\Controller\Session;

use Bol\CheckoutViaBol\Exception\CvbApiException;
use Bol\CheckoutViaBol\Service\CvbAvailabilityService;
use Bol\CheckoutViaBol\Service\CvbService;
use Bol\CheckoutViaBol\Service\RedirectService;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;

class Create implements HttpGetActionInterface
{
    public function __construct(
        private readonly Context                $context,
        private readonly CheckoutSession        $checkoutSession,
        private readonly CvbService             $cvbService,
        private readonly CvbAvailabilityService $availabilityService,
        private readonly ManagerInterface       $messageManager,
        private readonly UrlInterface           $urlBuilder
    )
    {
    }

    public function execute(): ResultInterface
    {
        $result = $this->context->getResultFactory()->create(ResultFactory::TYPE_JSON);

        if (!$this->allowCvbSessionCreate()) {
            throw new NotFoundException(__('Not found'));
        }

        try {
            $quote = $this->checkoutSession->getQuote();
        } catch (LocalizedException $e) {
            throw new NotFoundException(__('Not found'));
        }

        $cartUrl = $this->urlBuilder->getRouteUrl('checkout/cart');
        if (!$quote->hasItems()) {
            $this->messageManager->addErrorMessage('No items in cart');
            return $result->setData([
                'redirectUrl' => $cartUrl
            ]);
        }

        try {
            $sessionResponse = $this->cvbService->createCvbSession($quote);
            return $result->setData([
                'redirectUrl' => $sessionResponse['redirectUrl']
            ]);
        } catch (CvbApiException $e) {
            $this->messageManager->addErrorMessage('Checkout via Bol is currently unavailable');
            return $result->setData([
                'redirectUrl' => $cartUrl
            ]);
        }
    }

    private function allowCvbSessionCreate(): bool
    {
        return $this->availabilityService->isCvbFillInAvailable();
    }
}
