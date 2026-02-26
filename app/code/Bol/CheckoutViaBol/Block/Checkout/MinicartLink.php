<?php

namespace Bol\CheckoutViaBol\Block\Checkout;

use Magento\Catalog\Block\ShortcutInterface;

class MinicartLink extends Link implements ShortcutInterface
{
    public function getAlias()
    {
        return 'minicart.cvb.button.shortcut';
    }
}
