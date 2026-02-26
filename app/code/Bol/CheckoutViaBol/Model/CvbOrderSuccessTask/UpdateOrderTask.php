<?php

namespace Bol\CheckoutViaBol\Model\CvbOrderSuccessTask;

use Bol\CheckoutViaBol\Api\CvbOrderSuccessTaskInterface;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config;

class UpdateOrderTask implements CvbOrderSuccessTaskInterface
{
    public function __construct(private readonly Config $orderConfig)
    {
    }


    public function execute(Order $order, Transaction $transaction, string $cvbSessionId, string $cvbOrderId): void
    {
        /** @var \Magento\Sales\Api\Data\OrderPaymentInterface $payment */
        $payment = $order->getPayment();
        $payment->setParentTransactionId($cvbSessionId);
        $payment->setTransactionId($cvbOrderId);
        $payment->setIsTransactionPending(false);
        $payment->setIsTransactionClosed(true);

        $order->setState(Order::STATE_PROCESSING);
        # Todo make a configurable setting for merchants to select a status they want to use.
        $order->setStatus($this->orderConfig->getStateDefaultStatus(Order::STATE_PROCESSING));
        # Add order for saving
        $transaction->addObject($order);
    }
}
