<?php

namespace Bol\CheckoutViaBol\Model\CvbOrderSuccessTask;

use Bol\CheckoutViaBol\Api\CvbOrderSuccessTaskInterface;
use Bol\CheckoutViaBol\Model\Logger;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order;

class CompositeOrderSuccessTask implements CvbOrderSuccessTaskInterface
{
    /**
     * @param Logger                                     $logger
     * @param array<string,CvbOrderSuccessTaskInterface> $tasks
     */
    public function __construct(
        private readonly Logger $logger,
        private readonly array  $tasks = []
    ) {
        foreach ($tasks as $task) {
            if (!$task instanceof CvbOrderSuccessTaskInterface) {
                $message = sprintf(
                    'task must implement %s instead %s was given',
                    CvbOrderSuccessTaskInterface::class,
                    get_class($task)
                );
                throw new \InvalidArgumentException($message);
            }
        }
    }

    public function execute(Order $order, Transaction $transaction, string $cvbSessionId, string $cvbOrderId): void
    {
        /** @var CvbOrderSuccessTaskInterface $orderSuccessTask */
        foreach ($this->tasks as $taskName => $task) {
            $this->logger->logSuccessTaskMessage($taskName, $order, $cvbOrderId);
            $task->execute($order, $transaction, $cvbSessionId, $cvbOrderId);
        }
    }
}
