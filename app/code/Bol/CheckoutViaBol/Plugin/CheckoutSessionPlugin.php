<?php

namespace Bol\CheckoutViaBol\Plugin;

use Magento\Checkout\Model\Session as CheckoutSession;

class CheckoutSessionPlugin
{
    public function afterClearQuote(CheckoutSession $checkoutSession, $result)
    {
        # Also purge cvb session id if it is still there (for example when a different payment method is used).
        $checkoutSession->unsCvbSessionId();
        return $result;
    }
}
