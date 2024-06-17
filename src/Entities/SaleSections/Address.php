<?php

namespace Vitorccs\Maxipago\Entities\SaleSections;

use JsonSerializable;
use Vitorccs\Maxipago\Entities\Exportable;

class Address implements JsonSerializable
{
    use Exportable;

    const DEFAULT_COUNTRY = 'BR';

    public string $address;
    public ?string $address2;
    public ?string $district;
    public string $city;
    public string $state;
    public string $postalcode;
    public string $country;

    public function __construct(string  $address,
                                ?string $address2,
                                ?string  $district,
                                string  $city,
                                string  $state,
                                string  $postalCode,
                                ?string $country = null)
    {
        $this->address = $address;
        $this->address2 = $address2;
        $this->district = $district;
        $this->city = $city;
        $this->state = $state;
        $this->postalcode = $postalCode;
        $this->country = strtoupper($country ?: self::DEFAULT_COUNTRY);
    }
}
