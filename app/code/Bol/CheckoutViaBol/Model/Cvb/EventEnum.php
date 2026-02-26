<?php

namespace Bol\CheckoutViaBol\Model\Cvb;

enum EventEnum: string
{
    case REFUND = 'Refund';
    case SHIPPED = 'OrderShipped';
}
