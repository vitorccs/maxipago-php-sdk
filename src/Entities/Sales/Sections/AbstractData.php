<?php

namespace Vitorccs\Maxipago\Entities\Sales\Sections;

use JsonSerializable;
use Vitorccs\Maxipago\Entities\Exportable;

abstract class AbstractData implements JsonSerializable
{
    use Exportable;

    public string $name;
    public ?string $birthdate = null;
    public ?string $customerType = null;
    public ?string $email = null;
    public ?string $gender = null;
    public ?string $phone = null;
    public ?Address $address = null;
    public ?string $cpf = null;
    public ?string $rg = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function setAddressFields(string  $address,
                                     ?string $address2,
                                     string  $district,
                                     string  $city,
                                     string  $state,
                                     string  $postalcode,
                                     ?string $country = null): self
    {
        $this->address = new Address(
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

    public function nonExportableFields(): array
    {
        return [
            'address',
            'birthdate',
            'cpf',
            'rg',
            'customerType'
        ];
    }

    public function addExportableFields(): array
    {
        $addressFields = (array)$this->address ?: [];
        $documents = [];

        if (!empty($this->cpf)) {
            $documents[] = [
                'documentType' => 'CPF',
                'documentValue' => $this->cpf
            ];
        }

        if (!empty($this->rg)) {
            $documents[] = [
                'documentType' => 'RG',
                'documentValue' => $this->rg
            ];
        }

        $newFields = [
            'type' => $this->customerType,
            'birthDate' => $this->birthdate,
            'documents' => [
                'document' => $documents
            ]
        ];

        return array_merge($newFields, $addressFields);
    }
}
