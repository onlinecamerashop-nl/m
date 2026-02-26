<?php

namespace Bol\CheckoutViaBol\Api;

use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order;

interface CvbOrderSuccessTaskInterface
{
    /**
     * If there are exceptions in your task you are responsible for handling them yourself.
     * When all tasks complete $transaction->save is executed.
     *
     * @param Order       $order
     * @param Transaction $transaction
     * @param string      $cvbSessionId
     * @param string      $cvbOrderId
     *
     * @return void
     * @see Transaction::save()
     * You can add objects to the transaction and these will be saved as well.
     *
     */
    public function execute(
        Order       $order,
        Transaction $transaction,
        string      $cvbSessionId,
        string      $cvbOrderId,
    ): void;
}
