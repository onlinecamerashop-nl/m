<?php

namespace Bol\CheckoutViaBol\Controller\Order;

use Bol\CheckoutViaBol\Exception\CvbApiException;
use Bol\CheckoutViaBol\Model\Cvb\CvbSession;
use Bol\CheckoutViaBol\Model\CvbText;
use Bol\CheckoutViaBol\Model\Ui\ConfigProvider;
use Bol\CheckoutViaBol\Service\CvbOrderService;
use Bol\CheckoutViaBol\Service\RedirectService;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class Place implements HttpGetActionInterface
{
    public function __construct(
        private readonly Context                  $context,
        private readonly CvbOrderService          $orderService,
        private readonly CheckoutSession          $checkoutSession,
        private readonly RedirectService          $redirectService,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly SearchCriteriaBuilder    $searchCriteriaBuilder,
    ) {
    }

    public function execute(): ResultInterface
    {
        $cvbSessionData = CvbSession::fromRequest($this->context->getRequest());
        if (!$cvbSessionData->success || !$cvbSessionData->sid) {
            throw new NotFoundException(__('Not found'));
        }

        $sessionId                = $cvbSessionData->sid;
        $originalOrderIncrementId = $this->context->getRequest()->getParam('orderIncrementId');
        $order                    = $this->resolveOrder($originalOrderIncrementId);

        if (!$order) {
            throw new NotFoundException(__('Not found'));
        }

        try {
            $bolOrderReference = $this->orderService->placeCvbOrder($sessionId, $order);
            if (!$bolOrderReference) {
                # We should not get here, it is assumed in this controller that the user was redirected to bol
                # with a bnpl only session, successfully completed the steps there and now returns to magento.
                # When placeCvbOrder returns null it means that the session is somehow invalid. This should not happen
                # because the user just returned and the session was just created
                return $this->redirectService->redirectToCvbOrderError(CvbText::CVB_ORDER_ERROR_MESSAGE);
            }

            // When the order is not the same as on the session (due to browser -> app -> different browser)
            if ($this->checkoutSession->getLastRealOrder()->getIncrementId() !== $originalOrderIncrementId) {
                $this->checkoutSession->setLastRealOrderId($originalOrderIncrementId);
                $this->checkoutSession->setLastOrderId($order->getEntityId());
                $this->checkoutSession->setLastQuoteId($order->getQuoteId());
                $this->checkoutSession->setLastSuccessQuoteId($order->getQuoteId());
            }

            # Purge the cvb session reference after a successful place order call
            $this->checkoutSession->unsCvbSessionId();
            return $this->redirectService->redirectToCheckoutSuccess();
        } catch (CvbApiException $e) {
            # Something went wrong at the bol side
            return $this->redirectService->redirectToCvbOrderError(CvbText::CVB_ORDER_ERROR_MESSAGE);
        }
    }

    private function resolveOrder(string $cvbOrderIncrementId): ?Order
    {
        $order = $this->checkoutSession->getLastRealOrder();
        if ($order->getIncrementId() === $cvbOrderIncrementId) {
            return $order;
        }

        // Possibly the user switched browser, this can happen when the user returns from the BOL app when the original
        // browser used by the user is not the phone's default browser.
        /** @var Order $order */
        $order = $this->getOrderByIncrementId($cvbOrderIncrementId);
        if (!$order) {
            return null;
        }

        if ($order->getPayment()?->getMethod() !== ConfigProvider::CODE) {
            return null;
        }

        return $order;
    }

    /**
     * @param string $incrementId
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|null
     */
    private function getOrderByIncrementId(string $incrementId): ?OrderInterface
    {
        $this->searchCriteriaBuilder->addFilter('increment_id', $incrementId);

        $items = $this->orderRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return array_shift($items);
    }
}
