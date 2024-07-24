<?php

namespace Vitorccs\Maxipago\Builders;

use Vitorccs\Maxipago\Entities\PayTypes\OnFilePayType;
use Vitorccs\Maxipago\Entities\Sales\CreditCardSale;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;

class CreditCardTokenSaleBuilder extends AbstractSaleBuilder
{
    public function __construct(int    $customerId,
                                string $token,
                                string $referenceNum,
                                float  $chargeTotal)
    {
        $payType = new OnFilePayType($customerId, $token);

        $sale = new CreditCardSale(
            $payType,
            new Payment($chargeTotal),
            $referenceNum
        );

        parent::__construct($sale, $payType);
    }

    public static function create(int    $customerId,
                                  string $token,
                                  string $referenceNum,
                                  float  $chargeTotal): self
    {
        return new self(
            $customerId,
            $token,
            $referenceNum,
            $chargeTotal
        );
    }

    // force variable cast since PHP does not implement generics
    public function get(): CreditCardSale
    {
        return $this->sale;
    }
}
