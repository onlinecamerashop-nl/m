<?php

namespace Bol\CheckoutViaBol\Model\Cvb;

enum ShipmentEventStatusEnum: int
{
    case NOT_SENT = 0;
    case SUCCESSFULLY_SENT = 1;
    case TEMPORARY_ERROR = 2;
    case FINAL_ERROR = 3;
}
