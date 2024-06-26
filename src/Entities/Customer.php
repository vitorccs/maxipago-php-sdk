<?php

namespace Vitorccs\Maxipago\Entities;

use JsonSerializable;
use Vitorccs\Maxipago\Entities\Sales\Sections\Address;

class Customer implements JsonSerializable
{
    use Exportable;

    public string $customerIdExt;
    public string $firstName;
    public string $lastName;
    public ?string $phone = null;
    public ?string $email = null;
    public ?string $dob = null;
    public ?string $ssn = null;
    public ?string $sex = null;
    public ?Address $address = null;

    public function __construct(string $customerIdExt,
                                string $firstName,
                                string $lastName)
    {
        $this->customerIdExt = $customerIdExt;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function nonExportableFields(): array
    {
        return ['address'];
    }

    public function addExportableFields(): array
    {
        if (is_null($this->address)) return [];

        return [
            'zip' => $this->address->postalcode,
            'city' => $this->address->city,
            'state' => $this->address->state,
            'country' => $this->address->country,
            'address1' => $this->address->address,
            'address2' => $this->address->address2,
        ];
    }

    public function setAddressFields(string  $address,
                                     ?string $address2,
                                     string  $city,
                                     string  $state,
                                     string  $postalcode,
                                     ?string $country = null): self
    {
        $this->address = new Address(
            $address,
            $address2,
            null,
            $city,
            $state,
            $postalcode,
            $country
        );
        return $this;
    }
}
