<?php

namespace Bol\CheckoutViaBol\Cron;

use Bol\CheckoutViaBol\Api\CvbOrderRepositoryInterface;
use Bol\CheckoutViaBol\Api\Data\CvbOrderInterface;
use Bol\CheckoutViaBol\Model\Cvb\ShipmentEventStatusEnum;
use Bol\CheckoutViaBol\Service\CvbOrderService;
use Magento\Framework\Api\SearchCriteriaBuilder;

class RetrySendShipmentEventsCron
{
    public function __construct(
        private readonly CvbOrderRepositoryInterface $cvbOrderRepository,
        private readonly SearchCriteriaBuilder       $searchCriteriaBuilder,
        private readonly CvbOrderService             $cvbOrderService,
    ) {
    }

    /**
     * Retry all order send events which have not been successful and ar past their try again time.
     *
     * @return void
     */
    public function execute(): void
    {
        $criteria = $this->searchCriteriaBuilder
            ->addFilter(
                CvbOrderInterface::FIELD_CVB_ORDER_REFERENCE,
                true,
                'notnull'
            )
            ->addFilter(
                CvbOrderInterface::FIELD_TRY_SHIPMENT_EVENT_AGAIN_AT,
                new \DateTimeImmutable(),
                'lteq'
            )
            ->addFilter(
                CvbOrderInterface::FIELD_TRY_SHIPMENT_EVENT_AGAIN_AT,
                true,
                'notnull'
            )
            ->addFilter(
                CvbOrderInterface::FIELD_SHIPMENT_EVENT_STATUS,
                ShipmentEventStatusEnum::TEMPORARY_ERROR->value
            )
            ->create();

        $cvbOrders = $this->cvbOrderRepository->getList($criteria);
        foreach ($cvbOrders->getItems() as $cvbOrder) {
            $this->cvbOrderService->placeOrderShipment($cvbOrder);
        }
    }
}
