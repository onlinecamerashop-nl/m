<?php

namespace Bol\CheckoutViaBol\Service;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;

class CvbAvailabilityService
{
    private bool $shouldValidate = false;

    private ?bool $hideButtonCache = null;

    public function __construct(
        private readonly ConfigService   $configService,
        private readonly CheckoutSession $checkoutSession,
    ) {
    }

    public function isCvbAvailable(): bool
    {
        return $this->configService->isCvbEnabled()
            && $this->configService->isCvbTokenValid();
    }

    public function shouldHideCvbButtonInCheckout(): bool
    {
        if ($this->hideButtonCache !== null) {
            return $this->hideButtonCache;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $hideCvbButton = (bool)$this->checkoutSession->getHideCvbButton();
        if ($hideCvbButton) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->checkoutSession->unsHideCvbButton(); // Only hide once
        }

        // Cache it for the rest of this request
        $this->hideButtonCache = $hideCvbButton;
        return $this->hideButtonCache;
    }

    /**
     * @return bool
     */
    public function isCvbFillInAvailable(): bool
    {
        try {
            $isCustomerGuest = (bool)$this->checkoutSession->getQuote()->getCustomerIsGuest();
        } catch (LocalizedException $e) {
            $isCustomerGuest = true;
        }

        return $isCustomerGuest && $this->isCvbAvailable();
    }

    public function setShouldValidate(bool $shouldValidate): void
    {
        $this->shouldValidate = $shouldValidate;
    }

    public function isShouldValidate(): bool
    {
        return $this->shouldValidate;
    }
}
