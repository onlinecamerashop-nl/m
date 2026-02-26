<?php

namespace Bol\CheckoutViaBol\Controller\Order;

use Bol\CheckoutViaBol\Model\Ui\ConfigProvider;
use Bol\CheckoutViaBol\Service\RedirectService;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Model\Service\OrderService;

class Error implements HttpGetActionInterface
{
    public function __construct(
        private readonly CheckoutSession  $checkoutSession,
        private readonly OrderService     $orderService,
        private readonly RedirectService  $redirectService,
    ) {
    }

    public function execute(): ResultInterface
    {
        $order = $this->checkoutSession->getLastRealOrder();
        # Do not cancel if it is not a CVB order.
        if (!$order->getId() || $order->getPayment()?->getMethod() !== ConfigProvider::CODE) {
            throw new NotFoundException(__('Not found'));
        }

        # Purge session, we dont want old session ids floating around
        $this->checkoutSession->unsCvbSessionId();
        $this->orderService->cancel($order->getId());
        $this->checkoutSession->restoreQuote();

        return $this->redirectService->redirectToPaymentStep();
    }
}
