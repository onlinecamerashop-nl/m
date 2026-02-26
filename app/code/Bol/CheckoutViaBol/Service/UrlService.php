<?php

namespace Bol\CheckoutViaBol\Service;

use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * For now these urls are fixed, however with custom frontends and whatnot it could very well be that these values
 * can differ. This gives a way for developers to override these values via plugins or preference.
 * The best way to do this is to create an interface for this
 * and if there are custom requirements let another module implement this interface and preference it.
 */
class UrlService
{
    public function __construct(private readonly UrlInterface $urlBuilder)
    {
    }

    public function getCartPath(): string
    {
        return 'checkout/cart';
    }

    public function getCartUrl(): string
    {
        return $this->urlBuilder->getUrl($this->getCartPath());
    }

    public function getCheckoutUrl(): string
    {
        return $this->urlBuilder->getUrl('checkout');
    }

    public function getPaymentUrl(): string
    {
        return $this->urlBuilder->getUrl('checkout', ['_fragment' => 'payment']);
    }

    public function getCheckoutSuccessUrl(): string
    {
        return $this->urlBuilder->getUrl('checkout/onepage/success');
    }

    public function getCvbOrderErrorPath(): string
    {
        return 'cvb/order/error';
    }

    public function getCvbOrderErrorUrl(): string
    {
        return $this->urlBuilder->getUrl($this->getCvbOrderErrorPath());
    }

    public function getCvbOrderPlacePath(OrderInterface $order): string
    {
        return 'cvb/order/place?orderIncrementId=' . $order->getIncrementId();
    }

    public function getCvbSessionCreateUrl(): string
    {
        return $this->urlBuilder->getUrl('cvb/session/create');
    }

    public function getCvbSessionCheckoutPath(string $cartId): string
    {
        return 'cvb/session/checkout?cartId=' . $cartId;
    }

    public function getCvbSessionCheckoutUrl(): string
    {
        return $this->urlBuilder->getUrl('cvb/session/checkout');
    }
}
