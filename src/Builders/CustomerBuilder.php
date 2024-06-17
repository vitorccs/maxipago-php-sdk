<?php

namespace Vitorccs\Maxipago\Builders;

use Vitorccs\Maxipago\Entities\Customer;
use Vitorccs\Maxipago\Entities\SaleSections\Address;
use Vitorccs\Maxipago\Enums\CustomerGender;

class CustomerBuilder
{
    private Customer $customer;

    public function __construct(string $customerIdExt,
                                string $firstName,
                                string $lastName)
    {
        $this->customer = new Customer($customerIdExt, $firstName, $lastName);
    }

    public static function create(string $customerIdExt,
                                  string $firstName,
                                  string $lastName): self
    {
        return new self($customerIdExt, $firstName, $lastName);
    }

    public function get(): Customer
    {
        return $this->customer;
    }

    public function setPhone(?string $phone): self
    {
        $this->customer->phone = $phone;
        return $this;
    }

    public function setEmail(?string $email): self
    {
        $this->customer->email = $email;
        return $this;
    }

    public function setBirthDate(?\Datetime $date): self
    {
        $this->customer->dob = $date?->format('d/m/Y');
        return $this;
    }

    public function setSsn(?string $ssn): self
    {
        $this->customer->ssn = $ssn;
        return $this;
    }

    public function setGender(?CustomerGender $gender): self
    {
        $this->customer->sex = $gender?->value;
        return $this;
    }

    public function setAddress(?Address $address): self
    {
        $this->customer->address = $address;
        return $this;
    }

    public function setAddressFields(string  $address,
                                     ?string $address2,
                                     string  $city,
                                     string  $state,
                                     string  $postalcode,
                                     ?string $country = null): self
    {
        $this->customer->setAddressFields(
            $address,
            $address2,
            $city,
            $state,
            $postalcode,
            $country
        );
        return $this;
    }
}
