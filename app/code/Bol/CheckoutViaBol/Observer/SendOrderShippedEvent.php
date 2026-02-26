<?php

namespace Bol\CheckoutViaBol\Observer;

use Bol\CheckoutViaBol\Api\CvbOrderRepositoryInterface;
use Bol\CheckoutViaBol\Model\Cvb\ShipmentEventStatusEnum;
use Bol\CheckoutViaBol\Service\CvbOrderService;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;

class SendOrderShippedEvent implements ObserverInterface
{
    public function __construct(
        private readonly CvbOrderRepositoryInterface $cvbOrderRepository,
        private readonly CvbOrderService             $cvbOrderService
    ) {
    }

    public function execute(Observer $observer): void
    {
        /** @var Order\Shipment $shipment */
        $shipment = $observer->getEvent()->getShipment();
        $order    = $shipment->getOrder();

        try {
            $cvbOrder = $this->cvbOrderRepository->getByMagentoOrderId((int)$order->getId());
        } catch (NoSuchEntityException $e) {
            # If it is not cvb order then we do not need to send a shipment event
            return;
        }

        if (
            $cvbOrder->getShipmentEventStatus() !== ShipmentEventStatusEnum::NOT_SENT ||
            !$cvbOrder->getCvbOrderReference()
        ) {
            return;
        }

        $this->cvbOrderService->placeOrderShipment($cvbOrder);
    }
}
