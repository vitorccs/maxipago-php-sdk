<?php

namespace Vitorccs\Maxipago\Test\Builders;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Builders\CustomerBuilder;
use Vitorccs\Maxipago\Entities\Customer;
use Vitorccs\Maxipago\Entities\Sales\Sections\Address;
use Vitorccs\Maxipago\Enums\CustomerGender;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class CustomerBuilderTest extends TestCase
{
    #[DataProvider('requiredFieldsProvider')]
    public function teste_create_required_fields(string $customerIdExt,
                                                 string $firstName,
                                                 string $lastName)
    {
        $builder = CustomerBuilder::create($customerIdExt, $firstName, $lastName);
        $customer = $builder->get();

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertSame($customerIdExt, $customer->customerIdExt);
        $this->assertSame($firstName, $customer->firstName);
        $this->assertSame($lastName, $customer->lastName);
        $this->assertNull($customer->phone);
        $this->assertNull($customer->email);
        $this->assertNull($customer->dob);
        $this->assertNull($customer->sex);
        $this->assertNull($customer->address);
        $this->assertNull($customer->ssn);
    }

    #[DataProvider('optionalFieldsProvider')]
    public function teste_create_optional_fields(string          $customerIdExt,
                                                 string          $firstName,
                                                 string          $lastName,
                                                 ?string         $phone,
                                                 ?string         $email,
                                                 ?\DateTime      $dob,
                                                 ?CustomerGender $gender,
                                                 ?Address        $address)
    {
        $builder = CustomerBuilder::create($customerIdExt, $firstName, $lastName);
        $builder->setPhone($phone)
            ->setEmail($email)
            ->setBirthDate($dob)
            ->setGender($gender)
            ->setAddress($address);

        $customer = $builder->get();

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertSame($customerIdExt, $customer->customerIdExt);
        $this->assertSame($firstName, $customer->firstName);
        $this->assertSame($lastName, $customer->lastName);
        $this->assertSame($phone, $customer->phone);
        $this->assertSame($email, $customer->email);
        $this->assertSame($dob->format('d/m/Y'), $customer->dob);
        $this->assertSame($gender->value, $customer->sex);
        $this->assertSame($address, $customer->address);
    }

    public static function requiredFieldsProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'required' => [
                $faker->cpf(),
                $faker->firstName(),
                $faker->lastName(),
            ]
        ];
    }

    public static function optionalFieldsProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'optional with strings' => [
                $faker->cpf(),
                $faker->firstName(),
                $faker->lastName(),
                $faker->phoneNumber(),
                $faker->email(),
                $faker->dateTime(),
                FakerHelper::randomEnum(CustomerGender::class),
                new Address(
                    $faker->streetAddress(),
                    $faker->secondaryAddress(),
                    $faker->words(3, true),
                    $faker->city(),
                    $faker->stateAbbr(),
                    $faker->postcode(),
                    $faker->countryCode()
                ),
            ]
        ];
    }
}
