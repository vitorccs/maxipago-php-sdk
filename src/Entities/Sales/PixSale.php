<?php

namespace Vitorccs\Maxipago\Entities\Sales;

use Vitorccs\Maxipago\Entities\PayTypes\PixPayType;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;

class PixSale extends AbstractSale
{
    public function __construct(PixPayType $pixPayType,
                                Payment    $payment,
                                string     $referenceNum)
    {
        parent::__construct(
            $pixPayType,
            $payment,
            $referenceNum
        );
    }

    // force variable cast since PHP does not implement generics
    public function getPayType(): PixPayType
    {
        return $this->payType;
    }
}
