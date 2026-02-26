<?php

namespace Bol\CheckoutViaBol\Model;

class CvbText
{
    public const CVB_BUTTON_TEXT = 'Checkout via bol';
    public const CVB_BUTTON_EXPLANATION = 'Prefill and buy now, pay later using your bol account';

    public const CVB_ORDER_ERROR_MESSAGE = 'Checkout via bol is not available for this order. Please try a different payment method.';

    public const VALID_MERCHANT_CONFIG_MESSAGE = 'Merchant id and secret are valid.';
    public const INVALID_MERCHANT_CONFIG_MESSAGE = 'Merchant id or secret is invalid.';
    public const CANNOT_VALIDATE_CONFIG_MESSAGE = 'Could not validate configuration due to a temporary server error. bol is working on it, please try again later.';
    public const DEFAULT_MERCHANT_CONFIG_ERROR_MESSAGE = 'Something went wrong trying to validate the configuration.';
}
