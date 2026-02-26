<?php

namespace Bol\CheckoutViaBol\Observer;

use Bol\CheckoutViaBol\Service\CvbResourceService;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RefreshCvbResourcesObserver implements ObserverInterface
{
    public function __construct(
        private readonly CvbResourceService $cvbResourceService,
    )
    {
    }

    /**
     * refreshes cached cvb resources
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->cvbResourceService->refreshResources();
    }
}
