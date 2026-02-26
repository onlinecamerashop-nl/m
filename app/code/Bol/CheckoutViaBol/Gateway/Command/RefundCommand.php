<?php

namespace Bol\CheckoutViaBol\Gateway\Command;

use Bol\CheckoutViaBol\Service\CvbOrderService;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Api\OrderRepositoryInterface;

class RefundCommand implements CommandInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CvbOrderService          $cvbOrderService,
    ) {
    }

    /**
     * @param array $commandSubject
     *
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(array $commandSubject)
    {
        $payment = SubjectReader::readPayment($commandSubject);
        $amount  = (float)SubjectReader::readAmount($commandSubject);
        $orderId = (int)$payment->getOrder()->getId();
        /** @var \Magento\Sales\Model\Order $order */
        $order   = $this->orderRepository->get($orderId);

        $this->cvbOrderService->placeOrderRefund($order, $amount);
    }
}
