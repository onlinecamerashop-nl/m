<?php

namespace Bol\CheckoutViaBol\Model;

use Bol\CheckoutViaBol\Api\Data\CvbOrderInterface;
use Laminas\Http\Response;
use Magento\Framework\Logger\Monolog;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class Logger extends Monolog
{
    public function logCvbApiErrorResponse(Response $response): void
    {
        $this->error(
            'Cvb api error',
            [
                'status_code' => $response->getStatusCode(),
                'reason'      => $response->getReasonPhrase(),
                'body'        => (string)$response->getBody()
            ]
        );
    }

    public function logCreateFillInSession(CartInterface $cart): void
    {
        $this->info(
            'Creating fill in session',
            [
                'cart_id' => $cart->getId(),
                'items'   => array_map(
                    static fn (CartItemInterface $item) => $item->getName(),
                    $cart->getItems(),
                ),
            ]
        );
    }

    public function logCreateBnplSession(OrderInterface $order): void
    {
        $this->info(
            'Creating bnpl session',
            [
                'order_increment' => $order->getIncrementId(),
                'order_id'        => $order->getId(),
                'items'           => array_map(
                    static fn (OrderItemInterface $item) => $item->getName(),
                    $order->getItems(),
                ),
            ]
        );
    }

    public function logCreateOrder($cvbSessionId, OrderInterface $order): void
    {
        $this->info(
            'Converting cvb session to order',
            [
                'cvb_session_id'  => $cvbSessionId,
                'order_increment' => $order->getIncrementId(),
            ]
        );
    }

    public function logCreateShipment(CvbOrderInterface $cvbOrder): void
    {
        $this->info(
            'Sending shipment event',
            [
                'order_id'     => $cvbOrder->getOrderId(),
                'cvb_order_id' => $cvbOrder->getCvbOrderReference(),
            ]
        );
    }

    public function logCreateRefundEvent(CvbOrderInterface $cvbOrder, int $amountInCents): void
    {
        $this->info(
            'Sending refund event',
            [
                'order_id'     => $cvbOrder->getOrderId(),
                'cvb_order_id' => $cvbOrder->getCvbOrderReference(),
                'amount'       => $amountInCents,
            ]
        );
    }

    public function logStatsMessage(string $date): void
    {
        $this->info('Sending stats', ['date' => $date]);
    }

    public function logSuccessTaskMessage(string $taskName, OrderInterface $order, string $cvbOrderId): void
    {
        $this->info(
            'Running order success task',
            [
                'task_name'    => $taskName,
                'order_id'     => $order->getEntityId(),
                'cvb_order_id' => $cvbOrderId,
            ]
        );
    }

    public function logApiCall(string $method, string $path, ?array $payload = null): void
    {
        $this->debug('Api call', ['method' => $method, 'path' => $path, 'payload' => $payload]);
    }
}
