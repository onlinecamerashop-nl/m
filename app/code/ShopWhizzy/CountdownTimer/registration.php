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

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'ShopWhizzy_CountdownTimer',
    __DIR__
);
