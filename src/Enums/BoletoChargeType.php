<?php

namespace Vitorccs\Maxipago\Enums;

enum BoletoChargeType: string
{
    case PERCENTUAL = 'percentual';
    case AMOUNT = 'amount';
}
