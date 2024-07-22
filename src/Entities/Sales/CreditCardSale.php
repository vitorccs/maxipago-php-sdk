<?php

namespace Vitorccs\Maxipago\Entities\Sales;

use Vitorccs\Maxipago\Entities\PayTypes\CreditCardPayType;
use Vitorccs\Maxipago\Entities\PayTypes\OnFilePayType;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;

class CreditCardSale extends AbstractSale
{
    public function __construct(OnFilePayType|CreditCardPayType $onFilePayType,
                                Payment                         $payment,
                                string                          $referenceNum)
    {
        parent::__construct(
            $onFilePayType,
            $payment,
            $referenceNum
        );
    }

    // force variable cast since PHP does not implement generics
    public function getPayType(): OnFilePayType|CreditCardPayType
    {
        return $this->payType;
    }
}
