<?php

namespace Bol\CheckoutViaBol\Observer;

use Bol\CheckoutViaBol\Api\CvbOrderRepositoryInterface;
use Bol\CheckoutViaBol\Api\Data\CvbOrderInterfaceFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RegisterCvbOrderObserver implements ObserverInterface
{
    public function __construct(
        private readonly CheckoutSession             $checkoutSession,
        private readonly CvbOrderInterfaceFactory    $cvbOrderFactory,
        private readonly CvbOrderRepositoryInterface $cvbOrderRepository,
    ) {
    }

    /**
     * Saves a cvb order record if the order is placed when a cvb session id is available
     * This is for metric purposes and also to store the bol order reference if it actually is a bol order.
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        /**
         * When a cvbSessionId is available on the checkout session object it means that fill in session was used.
         * @see \Bol\CheckoutViaBol\Controller\Session\Checkout::execute()
         */
        $cvbSessionId = $this->checkoutSession->getCvbSessionId();
        if (!$cvbSessionId) {
            return;
        }

        if ((int)$this->checkoutSession->getQuoteId() !== (int)$quote->getId()) {
            return;
        }

        /** @var \Bol\CheckoutViaBol\Api\Data\CvbOrderInterface $cvbOrder */
        $cvbOrder = $this->cvbOrderFactory->create();
        $cvbOrder->setOrderId((int)$order->getId());
        $cvbOrder->setCvbSessionId($cvbSessionId);
        $cvbOrder->setIsFillInSession(true);
        $this->cvbOrderRepository->save($cvbOrder);
    }
}
