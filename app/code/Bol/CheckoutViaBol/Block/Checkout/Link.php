<?php

namespace Bol\CheckoutViaBol\Block\Checkout;

use Bol\CheckoutViaBol\Service\CvbAvailabilityService;
use Bol\CheckoutViaBol\Service\CvbResourceService;
use Bol\CheckoutViaBol\Service\UrlService;
use Magento\Framework\View\Element\Template;

class Link extends Template
{
    public function __construct(
        private readonly CvbAvailabilityService $availabilityService,
        private readonly UrlService             $urlService,
        private readonly CvbResourceService     $cvbResourceService,
        Template\Context                        $context,
        array                                   $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function isMethodAllowed(): bool
    {
        return $this->availabilityService->isCvbFillInAvailable();
    }

    public function displayCvbCheckoutButton(): bool
    {
        return !$this->availabilityService->shouldHideCvbButtonInCheckout();
    }

    public function getSessionCreateUrl(): string
    {
        return $this->urlService->getCvbSessionCreateUrl();
    }

    public function getButtonLabel(): string
    {
        return $this->cvbResourceService->getTitle();
    }

    public function getButtonDescription(): string
    {
        return $this->cvbResourceService->getDescription();
    }
}
