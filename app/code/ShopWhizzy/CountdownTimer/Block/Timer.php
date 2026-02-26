<?php
/**
 * ShopWhizzy Countdown Timer Module
 *
 * This file is part of the ShopWhizzy Countdown Timer module.
 * It displays a countdown timer for next day delivery options.
 *
 * @package   ShopWhizzy_CountdownTimer
 * @license   Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace ShopWhizzy\CountdownTimer\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class Timer extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Registry $registry
     * @param StockRegistryInterface $stockRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Registry $registry,
        StockRegistryInterface $stockRegistry,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->stockRegistry = $stockRegistry;
        parent::__construct($context, $data);

        // set the correct time zone
        date_default_timezone_set($this->_scopeConfig->getValue('general/locale/timezone', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    public function returnEnabled()
    {
        return $this->_scopeConfig->getValue('countdowntimer/wiz_shipping_settings/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function returnCountdownText()
    {
        return $this->_scopeConfig->getValue('countdowntimer/wiz_shipping_settings/countdown_time', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function isApplicable()
    {
        $onlyInStock = (bool)$this->_scopeConfig->getValue('countdowntimer/wiz_shipping_settings/only_in_stock', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (!$onlyInStock)
        {
            return true;
        }

        $product = $this->registry->registry('current_product');
        if (!$product)
        {
            return false;
        }

        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        return $stockItem->getIsInStock() && $stockItem->getQty() > 0;
    }
}