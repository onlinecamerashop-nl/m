<?php

namespace Bol\CheckoutViaBol\Service;

use Bol\CheckoutViaBol\Api\CvbOrderRepositoryInterface;
use Bol\CheckoutViaBol\Api\CvbOrderSuccessTaskInterface;
use Bol\CheckoutViaBol\Api\Data\CvbOrderInterface;
use Bol\CheckoutViaBol\Exception\CvbApiException;
use Bol\CheckoutViaBol\Model\Cvb\ShipmentEventStatusEnum;
use Bol\CheckoutViaBol\Model\Logger;
use Magento\Framework\DB\TransactionFactory;
use Magento\Sales\Model\Order;

class CvbOrderService
{
    public function __construct(
        private readonly CvbService                   $cvbService,
        private readonly CvbOrderRepositoryInterface  $cvbOrderRepository,
        private readonly CvbOrderSuccessTaskInterface $orderSuccessTask,
        private readonly TransactionFactory           $transactionFactory,
        private readonly Logger                       $logger,
    ) {
    }

    /**
     * @param string $cvbSessionId
     * @param Order  $order
     *
     * @return string|null
     * @throws \Bol\CheckoutViaBol\Exception\CvbApiException
     */
    public function placeCvbOrder(string $cvbSessionId, Order $order): ?string
    {
        try {
            $orderResponse = $this->cvbService->createOrder($cvbSessionId, $order);
            $cvbOrderId    = $orderResponse['id'];
        } catch (CvbApiException $e) {
            if ($e->getHttpResponse()->isClientError()) {
                # Session probably expired
                return null;
            }
            # Something really went wrong
            throw $e;
        }

        $transaction = $this->transactionFactory->create();
        $this->orderSuccessTask->execute($order, $transaction, $cvbSessionId, $cvbOrderId);
        $transaction->save();

        return $cvbOrderId;
    }

    /**
     * @param CvbOrderInterface $cvbOrder
     *
     * @return void
     */
    public function placeOrderShipment(CvbOrderInterface $cvbOrder): void
    {
        try {
            $this->logger->logCreateShipment($cvbOrder);
            $this->cvbService->createShipmentEvent($cvbOrder->getCvbOrderReference());
            $status = ShipmentEventStatusEnum::SUCCESSFULLY_SENT;
        } catch (CvbApiException $e) {
            $status = $e->getHttpResponse()->getStatusCode() === 404
                ? ShipmentEventStatusEnum::FINAL_ERROR
                : ShipmentEventStatusEnum::TEMPORARY_ERROR;

            if ($status === ShipmentEventStatusEnum::TEMPORARY_ERROR) {
                $delayInMinutes = min(2 ** ($cvbOrder->getShipmentEventTries() + 1), 60);
                $tryAgainAt     = new \DateTimeImmutable("+ $delayInMinutes minutes");
                $cvbOrder->setTryShipmentEventAgainAt($tryAgainAt);
            }
        }

        $cvbOrder->setShipmentEventTries($cvbOrder->getShipmentEventTries() + 1);
        $cvbOrder->setShipmentEventStatus($status);
        $this->cvbOrderRepository->save($cvbOrder);
    }

    /**
     * @param Order $order
     * @param float $amount
     *
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function placeOrderRefund(Order $order, float $amount): void
    {
        // No catching of missing entity exception, refunding should break if the bol order reference cannot be found
        $cvbOrder      = $this->cvbOrderRepository->getByMagentoOrderId((int)$order->getId());
        $amountInCents = (int)((float)$amount * 100);
        $this->logger->logCreateRefundEvent($cvbOrder, $amountInCents);
        # Also no catching of errors when create refund fails. Refunding is impossible without this api call.
        $this->cvbService->createRefundEvent(
            $cvbOrder->getCvbOrderReference(),
            $amountInCents
        );
    }
}
