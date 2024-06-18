<?php

namespace Vitorccs\Maxipago\Entities\PayTypes;

class BoletoPayType extends AbstractPayType
{
    public ?BoletoFields $charge;
    public ?BoletoFields $interestRate;
    public ?BoletoFields $discount;

    public int $number;
    public string $expirationDate;
    public string $format;
    public string $financialDocumentType;
    public ?string $instructions;

    public function __construct(int           $number,
                                string        $expirationDate,
                                ?BoletoFields $charge = null,
                                ?BoletoFields $interestRate = null,
                                ?BoletoFields $discount = null,
                                ?string       $format = 'pdf',
                                ?string       $financialDocumentType = 'DM',
                                ?string       $instructions = null)
    {
        $this->number = $number;
        $this->expirationDate = $expirationDate;
        $this->charge = $charge;
        $this->interestRate = $interestRate;
        $this->discount = $discount;
        $this->format = $format;
        $this->financialDocumentType = $financialDocumentType;
        $this->instructions = $instructions;
    }

    public function nodeName(): string
    {
        return 'boleto';
    }
}
