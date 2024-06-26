<?php

namespace Vitorccs\Maxipago\Entities\Sales;

use Vitorccs\Maxipago\Entities\PayTypes\BoletoPayType;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;

class BoletoSale extends AbstractSale
{
    public function __construct(BoletoPayType $boletoPayType,
                                Payment       $payment,
                                string        $referenceNum)
    {
        parent::__construct(
            $boletoPayType,
            $payment,
            $referenceNum
        );
    }

    // force variable cast since PHP does not implement generics
    public function getPayType(): BoletoPayType
    {
        return $this->payType;
    }
}
