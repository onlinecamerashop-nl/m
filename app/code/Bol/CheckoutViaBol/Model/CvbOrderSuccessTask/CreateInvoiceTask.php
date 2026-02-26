<?php

namespace Bol\CheckoutViaBol\Model\CvbOrderSuccessTask;

use Bol\CheckoutViaBol\Api\CvbOrderSuccessTaskInterface;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Service\InvoiceService;

class CreateInvoiceTask implements CvbOrderSuccessTaskInterface
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {
    }

    public function execute(Order $order, Transaction $transaction, string $cvbSessionId, string $cvbOrderId): void
    {
        $invoice = $this->invoiceService->prepareInvoice($order);
        $invoice->setTransactionId($cvbOrderId);
        $invoice->register();
        $invoice->pay();

        $transaction->addObject($invoice);
    }
}
