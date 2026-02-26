<?php

namespace Bol\CheckoutViaBol\Service;

use Bol\CheckoutViaBol\Api\CvbOrderRepositoryInterface;
use Bol\CheckoutViaBol\Api\Data\CvbOrderInterface;
use Bol\CheckoutViaBol\Model\Logger;
use DateTimeImmutable;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class CvbStatsService
{
    public function __construct(
        private readonly CvbApi                      $cvbApi,
        private readonly SearchCriteriaBuilder       $criteriaBuilder,
        private readonly OrderRepositoryInterface    $orderRepository,
        private readonly CvbOrderRepositoryInterface $cvbOrderRepository,
        private readonly Logger                      $logger,
    ) {
    }

    /**
     * @param DateTimeImmutable $date
     *
     * @return void
     */
    public function pushStats(DateTimeImmutable $date): void
    {
        $this->logger->logStatsMessage($date->format('Y-m-d'));
        $orders = $this->getOrders($date);
        # Extract those with a cvb session id (this id was available on the checkout session when the order was placed)
        $ordersWithCvbSession = $this->filterOrdersWithSession($orders);
        # From those extract the one which where actually placed with bnpl
        $cvbOrders = $this->filterCvbOrders($orders);
        $statsForDate = [
            'date'              => $date->format('Y-m-d'),
            'orderCount'        => [
                'total'      => count($orders),
                'cvbSession' => count($ordersWithCvbSession),
                'cvbOrder'   => count($cvbOrders)
            ],
            'orderValueInCents' => [
                'total'      => $this->getTotalInCents($orders),
                'cvbSession' => $this->getTotalInCents($ordersWithCvbSession),
                'cvbOrder'   => $this->getTotalInCents($cvbOrders)
            ]
        ];

        $stats = [
            'stats' => [
                $statsForDate
            ]
        ];
        $this->cvbApi->postMerchantStats($stats);
    }

    /**
     * @param DateTimeImmutable $date
     *
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    private function getOrders(DateTimeImmutable $date): array
    {
        $from     = $date->setTime(0, 0, 0);
        $to       = $from->add(\DateInterval::createFromDateString('+1 day'));
        $criteria = $this->criteriaBuilder
            ->addFilter('created_at', $from, 'gteq')
            ->addFilter('created_at', $to, 'lt')
            ->addFilter('state', Order::STATE_CANCELED, 'neq')
            ->create();

        return $this->orderRepository->getList($criteria)->getItems();
    }

    /**
     * @param array $orders
     *
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    private function filterOrdersWithSession(array $orders): array
    {
        $criteria = $this->criteriaBuilder
            ->addFilter(CvbOrderInterface::FIELD_ORDER_ID, array_keys($orders), 'in')
            ->addFilter(CvbOrderInterface::FIELD_IS_FILL_IN_SESSION, 1)
            ->create();

        $cvbSessionOrderIds = array_map(
            static fn (CvbOrderInterface $cvbOrder) => $cvbOrder->getOrderId(),
            $this->cvbOrderRepository->getList($criteria)->getItems(),
        );

        return array_intersect_key(
            $orders,
            array_flip($cvbSessionOrderIds)
        );
    }

    /**
     * @param array $orders
     *
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    private function filterCvbOrders(array $orders): array
    {
        $criteria = $this->criteriaBuilder
            ->addFilter(CvbOrderInterface::FIELD_ORDER_ID, array_keys($orders), 'in')
            ->addFilter(CvbOrderInterface::FIELD_CVB_ORDER_REFERENCE, true, 'notnull')
            ->create();

        $cvbOrderIds = array_map(
            static fn (CvbOrderInterface $cvbOrder) => $cvbOrder->getOrderId(),
            $this->cvbOrderRepository->getList($criteria)->getItems(),
        );

        return array_intersect_key(
            $orders,
            array_flip($cvbOrderIds),
        );
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface[] $orders
     *
     * @return int
     */
    private function getTotalInCents(array $orders): int
    {
        $total = array_sum(
            array_map(
                static fn (OrderInterface $order) => (float)$order->getGrandTotal(),
                $orders
            )
        );

        return (int)round((float)$total * 100);
    }
}
