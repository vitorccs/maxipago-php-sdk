<?php

namespace Vitorccs\Maxipago\Entities;

use JsonSerializable;

class CreditCard implements JsonSerializable
{
    use Exportable;

    public ?string $billingName = null;
    public ?string $billingAddress1 = null;
    public ?string $billingAddress2 = null;
    public ?string $billingCity = null;
    public ?string $billingState = null;
    public ?string $billingZip = null;
    public ?string $billingCountry = null;
    public ?string $billingPhone = null;
    public ?string $billingEmail = null;

    public function __construct(public int    $customerId,
                                public string $creditCardNumber,
                                public string $expirationMonth,
                                public int    $expirationYear)
    {

    }
}
