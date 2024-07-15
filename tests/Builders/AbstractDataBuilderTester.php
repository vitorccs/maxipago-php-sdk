<?php

namespace Vitorccs\Maxipago\Test\Builders;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Builders\AbstractDataBuilder;
use Vitorccs\Maxipago\Entities\Sales\Sections\Address;
use Vitorccs\Maxipago\Enums\CustomerGender;
use Vitorccs\Maxipago\Enums\CustomerType;
use Vitorccs\Maxipago\Helpers\CpfCnpjHelper;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

abstract class AbstractDataBuilderTester extends TestCase
{
    abstract public function getBuilder(string $name): AbstractDataBuilder;

    abstract public function instance(): string;

    #[DataProvider('requiredFieldsProvider')]
    public function test_create_required_fields(string $name)
    {
        $builder = $this->getBuilder($name);
        $data = $builder->get();

        $this->assertInstanceOf($this->instance(), $data);
        $this->assertSame($name, $data->name);
        $this->assertNull($data->birthdate);
        $this->assertNull($data->customerType);
        $this->assertNull($data->email);
        $this->assertNull($data->gender);
        $this->assertNull($data->phone);
        $this->assertNull($data->address);
        $this->assertNull($data->cpf);
        $this->assertNull($data->rg);
    }

    #[DataProvider('optionalFieldsProvider')]
    public function test_create_optional_fields(string          $name,
                                                ?\DateTime      $birthdate,
                                                ?CustomerType   $customerType,
                                                ?string         $email,
                                                ?CustomerGender $gender,
                                                ?string         $phone,
                                                ?string         $cpf,
                                                ?string         $cnpj,
                                                ?string         $rg)
    {
        $builder = $this->getBuilder($name);
        $builder->setBirthdate($birthdate);
        $builder->setCustomerType($customerType);
        $builder->setEmail($email);
        $builder->setGender($gender);
        $builder->setPhone($phone);
        $builder->setCpf($cpf);
        $builder->setCnpj($cnpj);
        $builder->setRg($rg);
        $data = $builder->get();

        $this->assertInstanceOf($this->instance(), $data);
        $this->assertSame($name, $data->name);
        $this->assertSame($birthdate?->format('Y-m-d'), $data->birthdate);
        $this->assertSame($customerType?->value, $data->customerType);
        $this->assertSame($email, $data->email);
        $this->assertSame($gender?->value, $data->gender);
        $this->assertSame($phone, $data->phone);
        $this->assertNull($data->address);
        $this->assertSame(CpfCnpjHelper::unmask($cpf), $data->cpf);
        $this->assertSame(CpfCnpjHelper::unmask($cnpj), $data->cnpj);
        $this->assertSame($rg, $data->rg);
    }

    #[DataProvider('cpfCnpjProvider')]
    public function test_set_cpf_cnpj_fields(string  $name,
                                             ?string $cpf,
                                             ?string $cnpj)
    {
        $cpfCnpj = $cpf ?: $cnpj;
        $builder = $this->getBuilder($name);
        $builder->setCpfCnpj($cpfCnpj);
        $data = $builder->get();

        $this->assertInstanceOf($this->instance(), $data);
        
        is_null($cpf)
            ? $this->assertNull($data->cpf)
            : $this->assertSame(CpfCnpjHelper::unmask($cpf), $data->cpf);

        is_null($cnpj)
            ? $this->assertNull($data->cnpj)
            : $this->assertSame(CpfCnpjHelper::unmask($cnpj), $data->cnpj);
    }

    #[DataProvider('addressFieldsProvider')]
    public function test_create_with_address(string  $address,
                                             ?string $address2,
                                             string  $district,
                                             string  $city,
                                             string  $state,
                                             string  $postalcode,
                                             ?string $country = null
    )
    {
        $address = new Address(
            $address,
            $address2,
            $district,
            $city,
            $state,
            $postalcode,
            $country
        );

        $builder = $this->getBuilder('');
        $builder->setAddress($address);
        $data = $builder->get();
        $addressData = $data->address;

        $this->assertSame($address->address, $addressData->address);
        $this->assertSame($address->address2, $addressData?->address2);
        $this->assertSame($address->district, $addressData->district);
        $this->assertSame($address->city, $addressData->city);
        $this->assertSame($address->state, $addressData->state);
        $this->assertSame($address->postalcode, $addressData->postalcode);
        $this->assertSame($address->country ?: Address::DEFAULT_COUNTRY, $addressData?->country);

        $this->assertInstanceOf($this->instance(), $data);
    }

    #[DataProvider('addressFieldsProvider')]
    public function test_set_address_fields(string  $address,
                                            ?string $address2,
                                            string  $district,
                                            string  $city,
                                            string  $state,
                                            string  $postalcode,
                                            ?string $country = null)
    {
        $builder = $this->getBuilder('');
        $builder->setAddressFields(
            $address,
            $address2,
            $district,
            $city,
            $state,
            $postalcode,
            $country
        );
        $billingData = $builder->get();
        $this->assertSame($address, $billingData->address?->address);
        $this->assertSame($address2, $billingData->address?->address2);
        $this->assertSame($district, $billingData->address?->district);
        $this->assertSame($city, $billingData->address?->city);
        $this->assertSame($state, $billingData->address?->state);
        $this->assertSame($postalcode, $billingData->address?->postalcode);
        $this->assertSame($country ?: Address::DEFAULT_COUNTRY, $billingData->address?->country);
    }

    public static function requiredFieldsProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'name' => [
                $faker->word()
            ]
        ];
    }

    public static function optionalFieldsProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'null values' => [
                $faker->word(),
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ],
            'non-null person' => [
                $faker->word(),
                $faker->dateTime(),
                FakerHelper::randomEnum(CustomerType::class),
                $faker->email(),
                FakerHelper::randomEnum(CustomerGender::class),
                $faker->cellphoneNumber(),
                $faker->cpf(),
                $faker->cnpj(),
                $faker->rg()
            ],
        ];
    }

    public static function cpfCnpjProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'cpf' => [
                $faker->word(),
                $faker->cpf(),
                null
            ],
            'cnpj' => [
                $faker->word(),
                null,
                $faker->cnpj()
            ],
            'none' => [
                $faker->word(),
                null,
                null
            ],
        ];
    }

    public static function addressFieldsProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'with required fields' => [
                $faker->streetAddress(),
                $faker->secondaryAddress(),
                $faker->words(3, true),
                $faker->city(),
                $faker->stateAbbr(),
                $faker->postcode(),
                $faker->countryCode()
            ],
            'with optional fields' => [
                $faker->streetAddress(),
                null,
                $faker->words(3, true),
                $faker->city(),
                $faker->stateAbbr(),
                $faker->postcode(),
                null
            ]
        ];
    }
}
