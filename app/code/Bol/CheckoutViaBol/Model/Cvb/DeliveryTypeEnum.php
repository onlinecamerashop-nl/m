<?php

namespace Bol\CheckoutViaBol\Model\Cvb;

enum DeliveryTypeEnum: string
{
    case OTHER = 'OTHER';
    case PRODUCT = 'PRODUCT';
    case DELIVERY = 'DELIVERY';
}
