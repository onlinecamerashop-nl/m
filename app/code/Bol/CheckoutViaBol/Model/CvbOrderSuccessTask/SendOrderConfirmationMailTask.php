<?php

namespace Bol\CheckoutViaBol\Model\CvbOrderSuccessTask;

use Bol\CheckoutViaBol\Api\CvbOrderSuccessTaskInterface;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;

class SendOrderConfirmationMailTask implements CvbOrderSuccessTaskInterface
{
    public function __construct(
        private readonly OrderSender   $orderSender,
        private readonly OrderIdentity $orderIdentityContainer,
    ) {
    }

    public function execute(Order $order, Transaction $transaction, string $cvbSessionId, string $cvbOrderId): void
    {
        if ($order->getEmailSent() || !$this->orderIdentityContainer->isEnabled()) {
            return;
        }

        # The send method sets the fact that it has already been sent so that it won't send again afterwards
        $this->orderSender->send($order);
    }
}
