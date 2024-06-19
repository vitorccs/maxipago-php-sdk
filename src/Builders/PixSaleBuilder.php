<?php

namespace Vitorccs\Maxipago\Builders;

use Vitorccs\Maxipago\Entities\PayTypes\PixPayType;
use Vitorccs\Maxipago\Entities\Sales\PixSale;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;
use Vitorccs\Maxipago\Enums\Processor;

class PixSaleBuilder extends AbstractSaleBuilder
{
    public function __construct(Processor $processor,
                                float     $chargeTotal,
                                string    $referenceNum,
                                int       $expirationTime)
    {
        $payType = new PixPayType($expirationTime);

        $sale = new PixSale(
            $payType,
            new Payment($chargeTotal),
            $referenceNum,
            $processor->value
        );

        parent::__construct($sale, $payType);
    }

    public static function create(Processor $processor,
                                  float     $chargeTotal,
                                  string    $referenceNum,
                                  int       $expirationTime): self
    {
        return new self(
            $processor,
            $chargeTotal,
            $referenceNum,
            $expirationTime
        );
    }

    // force variable cast since PHP does not implement generics
    public function get(): PixSale
    {
        return $this->sale;
    }

    public function setPixPaymentInfo(?string $info): self
    {
        $this->payType->paymentInfo = $info;
        return $this;
    }
}
