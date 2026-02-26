<?php

namespace Bol\CheckoutViaBol\Service;

use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;

class ConfigService
{
    private const PATH_CVB_ACTIVE = 'payment/cvb/active';
    private const PATH_MERCHANT_ID = 'payment/cvb/merchant_id';
    private const PATH_MERCHANT_SECRET = 'payment/cvb/merchant_secret';
    private const PATH_DEBUG_MODE = 'payment/cvb/debug_mode';
    private const PATH_STAGING_MODE = 'bol/checkout_via_bol/staging_mode';
    private const PATH_VALID_MERCHANT_CONFIG = 'bol/checkout_via_bol/is_merchant_config_valid';

    public function __construct(
        private readonly ScopeConfigInterface      $scopeConfig,
        private readonly WriterInterface           $configWriter,
        private readonly ReinitableConfigInterface $appConfig,
    ) {
    }

    public function isCvbEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::PATH_CVB_ACTIVE);
    }

    public function getMerchantId(): ?string
    {
        return $this->scopeConfig->getValue(self::PATH_MERCHANT_ID);
    }

    public function getMerchantSecret(): ?string
    {
        return $this->scopeConfig->getValue(self::PATH_MERCHANT_SECRET);
    }

    public function isDebugMode(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::PATH_DEBUG_MODE);
    }

    /**
     * This is cannot be modified by the merchant in the admin panel but we do want some mechanism to talk to the
     * staging api. If you want to set this, add it directly in 'core_config_data' or add it in app/etc/env.php
     *
     * @return bool
     */
    public function isStagingMode(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::PATH_STAGING_MODE);
    }

    /**
     * This cannot be modified directly but is set when merchant id and secret are changed in the sytem config
     * via the admin panel
     *
     * @return bool
     */
    public function isCvbTokenValid(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::PATH_VALID_MERCHANT_CONFIG);
    }

    /**
     * @param bool $isValid
     *
     * @return void
     */
    public function writeIsTokenValid(bool $isValid): void
    {
        $this->configWriter->save(self::PATH_VALID_MERCHANT_CONFIG, $isValid ? 1 : 0);
        $this->appConfig->reinit();
    }

    /**
     * Looks like magento does not expose this directly via a config provider class
     *
     * @return int
     */
    public function getAddressStreetLineCount(): int
    {
        return (int)$this->scopeConfig->getValue('customer/address/street_lines');
    }
}
