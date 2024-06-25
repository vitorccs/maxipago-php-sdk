<?php

namespace Vitorccs\Maxipago\Entities\PayTypes;

class BoletoPayType extends AbstractPayType
{
    const DEFAULT_FORMAT = 'pdf';
    const DEFAULT_FIN_DOC_TYPE = 'DM';

    public ?BoletoFields $charge;
    public ?BoletoFields $interestRate;
    public ?BoletoFields $discount;
    public ?int $number;
    public string $expirationDate;
    public string $format;
    public string $financialDocumentType;
    public ?string $instructions;

    public function __construct(string        $expirationDate,
                                ?int          $number = null,
                                ?BoletoFields $charge = null,
                                ?BoletoFields $interestRate = null,
                                ?BoletoFields $discount = null,
                                ?string       $format = null,
                                ?string       $financialDocumentType = null,
                                ?string       $instructions = null)
    {
        $this->expirationDate = $expirationDate;
        $this->number = $number;
        $this->charge = $charge;
        $this->interestRate = $interestRate;
        $this->discount = $discount;
        $this->format = $format ?: self::DEFAULT_FORMAT;
        $this->financialDocumentType = $financialDocumentType ?: self::DEFAULT_FIN_DOC_TYPE;
        $this->instructions = $instructions;
    }

    public function nodeName(): string
    {
        return 'boleto';
    }
}
