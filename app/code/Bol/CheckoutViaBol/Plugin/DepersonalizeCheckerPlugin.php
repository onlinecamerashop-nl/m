<?php

namespace Bol\CheckoutViaBol\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Framework\View\LayoutInterface;
use Magento\PageCache\Model\DepersonalizeChecker;

class DepersonalizeCheckerPlugin
{
    public function __construct(private readonly Http $request)
    {
    }

    /**
     * @param DepersonalizeChecker $checker
     * @param bool                 $result
     * @param LayoutInterface      $layout
     *
     * @return bool
     */
    public function afterCheckIfDepersonalize(
        DepersonalizeChecker $checker,
        bool $result,
        LayoutInterface $layout
    ): bool
    {
        // When sending the order confirmation mail the depersonalize checker will destroy the checkout session
        // This will result us not being able to send the customer to the checkout success page. We prevent this behaviour
        // only on the cvb order place url. Not sure if there is a more elegant solution. The checkout success page
        // will purge the checkout session anyway.
        return $result && ($this->request->getPathInfo() !== '/cvb/order/place');
    }
}
