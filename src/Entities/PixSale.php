<?php

namespace Vitorccs\Maxipago\Entities;

use Vitorccs\Maxipago\Entities\SaleSections\Payment;
use Vitorccs\Maxipago\Entities\SaleSections\PixPayType;

class PixSale extends AbstractSale
{
    public function __construct(PixPayType  $pixPayType,
                                Payment     $payment,
                                string      $referenceNum,
                                int         $processorID)
    {
        parent::__construct(
            $pixPayType,
            $payment,
            $referenceNum,
            $processorID
        );
    }

    // force variable cast since PHP does not implement generics
    public function getPayType(): PixPayType
    {
        return $this->payType;
    }
}
