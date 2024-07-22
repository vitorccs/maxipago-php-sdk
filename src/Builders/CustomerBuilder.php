<?php

namespace Vitorccs\Maxipago\Builders;

use Vitorccs\Maxipago\Entities\Customer;
use Vitorccs\Maxipago\Entities\Sales\Sections\Address;
use Vitorccs\Maxipago\Enums\CustomerGender;
use Vitorccs\Maxipago\Exceptions\MaxipagoException;
use Vitorccs\Maxipago\Helpers\DateHelper;

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

    public function setCustomerId(int $customerId): self
    {
        $this->customer->customerId = $customerId;
        return $this;
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

    /**
     * @throws MaxipagoException
     */
    public function setBirthDate(\Datetime|string|null $date): self
    {
        $this->customer->dob = DateHelper::toLocalString($date);
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
                                     string  $postalCode,
                                     ?string $country = null): self
    {
        $this->customer->setAddressFields(
            $address,
            $address2,
            $city,
            $state,
            $postalCode,
            $country
        );
        return $this;
    }
}
