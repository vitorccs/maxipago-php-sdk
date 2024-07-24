<?php

namespace Vitorccs\Maxipago\Builders;

use Vitorccs\Maxipago\Entities\PayTypes\CreditCardPayType;
use Vitorccs\Maxipago\Entities\Sales\CreditCardSale;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;

class CreditCardSaleBuilder extends AbstractSaleBuilder
{
    public function __construct(string     $creditCardNumber,
                                string|int $expirationMonth,
                                string|int $expirationYear,
                                string     $cvvNumber,
                                string     $referenceNum,
                                float      $chargeTotal)
    {
        $payType = new CreditCardPayType(
            $creditCardNumber,
            $expirationMonth,
            $expirationYear,
            $cvvNumber
        );

        $sale = new CreditCardSale(
            $payType,
            new Payment($chargeTotal),
            $referenceNum
        );

        parent::__construct($sale, $payType);
    }

    public static function create(string     $creditCardNumber,
                                  string|int $expirationMonth,
                                  string|int $expirationYear,
                                  string     $cvvNumber,
                                  string     $referenceNum,
                                  float      $chargeTotal): self
    {
        return new self(
            $creditCardNumber,
            $expirationMonth,
            $expirationYear,
            $cvvNumber,
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
