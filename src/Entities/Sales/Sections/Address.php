<?php

namespace Vitorccs\Maxipago\Entities\Sales\Sections;

use JsonSerializable;
use Vitorccs\Maxipago\Entities\Exportable;

class Address implements JsonSerializable
{
    use Exportable;

    const string DEFAULT_COUNTRY = 'BR';

    public function __construct(public string  $address,
                                public ?string $address2,
                                public ?string $district,
                                public string  $city,
                                public string  $state,
                                public string  $postalCode,
                                ?string        $country = null)
    {
        $this->country = strtoupper($country ?: self::DEFAULT_COUNTRY);
    }

    // declared after the constructor to preserve the XML node order (see Exportable)
    public string $country;

    public function nonExportableFields(): array
    {
        return [
            'postalCode'
        ];
    }

    public function addExportableFields(): array
    {
        return [
            'postalcode' => $this->postalCode,
        ];
    }
}
