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

namespace ShopWhizzy\CountdownTimer\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 *	@var $scopeConfig
	 */
	protected $scopeConfig;

	/**
	 *	@param \Magento\Framework\App\Config\scopeConfigInterface $scopeConfig
	 */
	public function __construct(
	 \Magento\Framework\App\Config\scopeConfigInterface $scopeConfig
	) {
		$this->scopeConfig = $scopeConfig;
	}

	public function getConfig($key, $type = "exact")
	{
		$val = $this->scopeConfig->getValue(
		 "countdowntimer/settings/",
		 \Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);

		if ($type == "bool")
		{
			$val = filter_var($val, FILTER_VALIDATE_BOOLEAN);
		}

		return $val;
	}
}