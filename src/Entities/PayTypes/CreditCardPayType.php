<?php

namespace Vitorccs\Maxipago\Entities\PayTypes;

use Vitorccs\Maxipago\Entities\Exportable;
use Vitorccs\Maxipago\Helpers\CreditCardHelper;

class CreditCardPayType extends AbstractPayType
{
    use Exportable;

    public string $number;
    public string $expMonth;
    public string $expYear;

    public function __construct(string        $number,
                                string|int    $expMonth,
                                string|int    $expYear,
                                public string $cvvNumber)
    {
        $this->expMonth = CreditCardHelper::normalizeMonth($expMonth);
        $this->number = CreditCardHelper::unmaskNumber($number);
        $this->expYear = (string)$expYear;
    }

    public function nodeName(): string
    {
        return 'creditCard';
    }
}
