<?php

namespace Vitorccs\Maxipago\Builders;

use Vitorccs\Maxipago\Entities\Sales\Sections\AbstractData;
use Vitorccs\Maxipago\Entities\Sales\Sections\Address;
use Vitorccs\Maxipago\Enums\CustomerGender;
use Vitorccs\Maxipago\Enums\CustomerType;
use Vitorccs\Maxipago\Helpers\CpfCnpjHelper;
use Vitorccs\Maxipago\Helpers\DateHelper;

abstract class AbstractDataBuilder
{
    protected AbstractData $data;

    public function __construct(AbstractData $data)
    {
        $this->data = $data;
    }

    public function get(): AbstractData
    {
        return $this->data;
    }

    public function setAddress(?Address $address): self
    {
        $this->data->address = $address;
        return $this;
    }

    public function setBirthdate(?\DateTime $date): self
    {
        $this->data->birthdate = DateHelper::toString($date);
        return $this;
    }

    /**
     * Note: some Processors such as "ITAUPIX" fails to create new
     * sale orders if value contains non-numeric chars
     */
    public function setCpf(?string $cpf): self
    {
        $this->data->cpf = CpfCnpjHelper::unmask($cpf);
        return $this;
    }

    public function setEmail(?string $email): self
    {
        $this->data->email = $email;
        return $this;
    }

    public function setGender(?CustomerGender $gender): self
    {
        $this->data->gender = $gender?->value;
        return $this;
    }

    public function setPhone(?string $phone): self
    {
        $this->data->phone = $phone;
        return $this;
    }

    public function setRg(?string $rg): self
    {
        $this->data->rg = $rg;
        return $this;
    }

    public function setCustomerType(?CustomerType $customerType): self
    {
        $this->data->customerType = $customerType?->value;
        return $this;
    }

    public function setAddressFields(string  $address,
                                     ?string $address2,
                                     string  $district,
                                     string  $city,
                                     string  $state,
                                     string  $postalcode,
                                     ?string $country = null): self
    {
        $this->data->setAddressFields(
            $address,
            $address2,
            $district,
            $city,
            $state,
            $postalcode,
            $country
        );
        return $this;
    }
}
