<?php

namespace Vitorccs\Maxipago\Builders;

use Vitorccs\Maxipago\Entities\PayTypes\BoletoFields;
use Vitorccs\Maxipago\Entities\PayTypes\BoletoPayType;
use Vitorccs\Maxipago\Entities\Sales\BoletoSale;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;
use Vitorccs\Maxipago\Enums\BoletoChargeType;
use Vitorccs\Maxipago\Exceptions\MaxipagoException;
use Vitorccs\Maxipago\Helpers\DateHelper;

class BoletoSaleBuilder extends AbstractSaleBuilder
{
    public function __construct(float            $chargeTotal,
                                string           $referenceNum,
                                \Datetime|string $expirationDate,
                                ?int             $number = null)
    {
        $expirationDate = DateHelper::toString($expirationDate);

        $payType = new BoletoPayType($expirationDate, $number);

        $sale = new BoletoSale(
            $payType,
            new Payment($chargeTotal),
            $referenceNum
        );

        parent::__construct($sale, $payType);
    }

    public static function create(float            $chargeTotal,
                                  string           $referenceNum,
                                  \Datetime|string $expirationDate,
                                  ?int             $number = null): self
    {
        return new self(
            $chargeTotal,
            $referenceNum,
            $expirationDate,
            $number,
        );
    }

    // force variable cast since PHP does not implement generics
    public function get(): BoletoSale
    {
        return $this->sale;
    }

    /**
     * @throws MaxipagoException
     */
    public function setCharge(\Datetime|string $date,
                              BoletoChargeType $type,
                              float            $value): self
    {
        $date = DateHelper::toString($date);

        $this->payType->charge = new BoletoFields($date, $type->value, $value);

        return $this;
    }

    /**
     * @throws MaxipagoException
     */
    public function setInterestRate(\Datetime|string $date,
                                    float            $value): self
    {
        $date = DateHelper::toString($date);
        $type = BoletoChargeType::PERCENTUAL;

        $this->payType->interestRate = new BoletoFields($date, $type->value, $value, true);
        return $this;
    }

    /**
     * @throws MaxipagoException
     */
    public function setDiscount(\Datetime|string $date,
                                float            $value): self
    {
        $date = DateHelper::toString($date);
        $type = BoletoChargeType::PERCENTUAL;

        $this->payType->discount = new BoletoFields($date, $type->value, $value);
        return $this;
    }

    public function setFormat(string $format): self
    {
        $this->payType->format = $format;
        return $this;
    }

    public function setFinancialDocumentType(string $type): self
    {
        $this->payType->financialDocumentType = $type;
        return $this;
    }

    public function setInstructions(string $instructions): self
    {
        $this->payType->instructions = $instructions;
        return $this;
    }
}
