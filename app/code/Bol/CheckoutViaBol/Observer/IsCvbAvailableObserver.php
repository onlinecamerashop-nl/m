<?php

namespace Bol\CheckoutViaBol\Observer;

use Bol\CheckoutViaBol\Model\Ui\ConfigProvider;
use Bol\CheckoutViaBol\Service\CvbAvailabilityService;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class IsCvbAvailableObserver implements ObserverInterface
{
    public function __construct(private readonly CvbAvailabilityService $availabilityService)
    {
    }

    /**
     * We hoped to make use of the 'availability' validator in
     * @see \Magento\Payment\Model\Method\Adapter::isAvailable()
     * But at that point the info instance is not yet set... For that reason we use this observer implementation
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer): void
    {
        /** @var \Magento\Payment\Model\MethodInterface $methodInstance */
        $methodInstance = $observer->getEvent()->getData('method_instance');
        if ($methodInstance->getCode() !== ConfigProvider::CODE) {
            // Don't do anything if this is not the CVB method.
            return;
        }

        /** @var DataObject $result */
        $result = $observer->getEvent()->getData('result');
        // Also check if tokens are valid
        $result->setData(
            'is_available',
            $result->getData('is_available') && $this->availabilityService->isCvbAvailable()
        );
    }
}
