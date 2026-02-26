<?php

namespace Bol\CheckoutViaBol\Model;

use Bol\CheckoutViaBol\Service\CvbAvailabilityService;
use Bol\CheckoutViaBol\Service\CvbResourceService;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Asset\Repository;

class CvbCheckoutConfigProvider implements ConfigProviderInterface
{
    public function __construct(
        private readonly CvbAvailabilityService $availabilityService,
        private readonly Session                $checkoutSession,
        private readonly CvbResourceService     $cvbResourceService,
        private readonly Repository $assetRepo
    )
    {
    }

    public function getConfig(): array
    {
        return [
            'cvb' => [
                'available' => $this->availabilityService->isCvbFillInAvailable(),
                'isFillInSession' => $this->checkoutSession->hasCvbSessionId(),
                'isBnplSelected' => $this->checkoutSession->getIsBnplSelected(),
                'hideCvbButtonInCheckout' => $this->availabilityService->shouldHideCvbButtonInCheckout(),
                'paymentLogo' => $this->assetRepo->getUrl('Bol_CheckoutViaBol::images/cvb-payment-logo.svg'),
                'texts' => [
                    'title' => $this->cvbResourceService->getTitle(),
                    'description' => $this->cvbResourceService->getDescription(),
                ]
            ],
        ];
    }
}
