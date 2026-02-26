<?php

namespace Bol\CheckoutViaBol\Gateway\Command;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config;

class InitializeCommand implements CommandInterface
{
    public function __construct(private readonly Config $orderConfig)
    {
    }

    public function execute(array $commandSubject): void
    {
        $stateObject = SubjectReader::readStateObject($commandSubject);
        $stateObject->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);
        $stateObject->setData(OrderInterface::STATUS, $this->resolveOrderStatus($commandSubject));
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = SubjectReader::readPayment($commandSubject)->getPayment();
        $order = $payment->getOrder();
        $order->setCanSendNewEmailFlag(false);
    }

    private function resolveOrderStatus(array $commandSubject): string
    {
        $defaultStatus = $this->orderConfig->getStateDefaultStatus(Order::STATE_PENDING_PAYMENT);
        try {
            $status = SubjectReader::readPayment($commandSubject)
                ->getPayment()
                ->getMethodInstance()
                ->getConfigData('order_status');
            # $status should always be filled however this is unsure from this context
            return $status ?: $defaultStatus;
        } catch (LocalizedException $e) {
            return $defaultStatus;
        }
    }
}
