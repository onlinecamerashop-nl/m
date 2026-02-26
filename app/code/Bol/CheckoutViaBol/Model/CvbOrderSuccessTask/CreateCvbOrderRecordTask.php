<?php

namespace Bol\CheckoutViaBol\Model\CvbOrderSuccessTask;

use Bol\CheckoutViaBol\Api\CvbOrderRepositoryInterface;
use Bol\CheckoutViaBol\Api\CvbOrderSuccessTaskInterface;
use Bol\CheckoutViaBol\Api\Data\CvbOrderInterface;
use Bol\CheckoutViaBol\Api\Data\CvbOrderInterfaceFactory;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;

class CreateCvbOrderRecordTask implements CvbOrderSuccessTaskInterface
{
    public function __construct(
        private readonly CvbOrderInterfaceFactory    $cvbOrderFactory,
        private readonly CvbOrderRepositoryInterface $cvbOrderRepository,
    ) {
    }

    public function execute(Order $order, Transaction $transaction, string $cvbSessionId, string $cvbOrderId): void
    {
        /** @var CvbOrderInterface $cvbOrderRecord */
        try {
            # Is created on the quote submit when it is a fill in session and updated here with the bol order reference
            $cvbOrderRecord = $this->cvbOrderRepository->getByMagentoOrderId((int)$order->getEntityId());
        } catch (NoSuchEntityException $e) {
            # When it is a bnpl only order
            $cvbOrderRecord = $this->cvbOrderFactory->create();
            $cvbOrderRecord->setOrderId((int)$order->getEntityId());
            $cvbOrderRecord->setCvbSessionId($cvbSessionId);
            $cvbOrderRecord->setIsFillInSession(false);
        }

        $cvbOrderRecord->setCvbOrderReference($cvbOrderId);

        $saveCvbRecord = function () use ($cvbOrderRecord) {
            $this->cvbOrderRepository->save($cvbOrderRecord);
        };

        $transaction->addCommitCallback($saveCvbRecord);
    }
}
