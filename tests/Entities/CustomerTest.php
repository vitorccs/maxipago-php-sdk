<?php

namespace Vitorccs\Maxipago\Test\Entities;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Entities\Customer;
use Vitorccs\Maxipago\Entities\Sales\Sections\Address;
use Vitorccs\Maxipago\Enums\CustomerGender;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class CustomerTest extends TestCase
{
    #[DataProvider('customerFieldsProvider')]
    public function test_export(string   $customerIdExt,
                                string   $firstName,
                                string   $lastName,
                                ?string  $phone,
                                ?string  $email,
                                ?string  $dob,
                                ?string  $gender,
                                ?Address $address)
    {
        $customer = new Customer($customerIdExt, $firstName, $lastName);
        $customer->phone = $phone;
        $customer->email = $email;
        $customer->dob = $dob;
        $customer->sex = $gender;
        $customer->address = $address;
        $export = $customer->export();

        $this->assertSame($customerIdExt, $export['customerIdExt']);
        $this->assertSame($firstName, $export['firstName']);
        $this->assertSame($lastName, $export['lastName']);
        $this->assertSame($phone, $export['phone']);
        $this->assertSame($email, $export['email']);
        $this->assertSame($dob, $export['dob']);
        $this->assertSame($gender, $export['sex']);
        $this->assertSame($address?->postalCode, $export['zip'] ?? null);
        $this->assertSame($address?->address2, $export['address2'] ?? null);
        $this->assertSame($address?->address2, $export['address2'] ?? null);
        $this->assertSame($address?->city, $export['city'] ?? null);
        $this->assertSame($address?->state, $export['state'] ?? null);
        $this->assertSame($address?->country, $export['country'] ?? null);
    }

    public static function customerFieldsProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'required fields' => [
                $faker->cpf(),
                $faker->firstName(),
                $faker->lastName(),
                null,
                null,
                null,
                null,
                null,
            ],
            'optional fields' => [
                $faker->cpf(),
                $faker->firstName(),
                $faker->lastName(),
                $faker->phoneNumber(),
                $faker->email(),
                $faker->date(),
                FakerHelper::randomEnumValue(CustomerGender::class),
                new Address(
                    $faker->streetAddress(),
                    $faker->secondaryAddress(),
                    $faker->words(3, true),
                    $faker->city(),
                    $faker->stateAbbr(),
                    $faker->postcode(),
                    $faker->countryCode()
                ),
            ],
        ];
    }
}
