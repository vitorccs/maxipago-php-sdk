<?php

namespace Vitorccs\Maxipago\Test\Entities\SaleSections;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Entities\SaleSections\AbstractData;
use Vitorccs\Maxipago\Entities\SaleSections\Address;
use Vitorccs\Maxipago\Enums\CustomerGender;
use Vitorccs\Maxipago\Enums\CustomerType;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class AbstractDataTest extends TestCase
{
    #[DataProvider('dataProvider')]
    public function test_export(string   $name,
                                ?string  $birthdate,
                                ?string  $customerType,
                                ?string  $email,
                                ?string  $gender,
                                ?string  $phone,
                                ?Address $address,
                                ?string  $cpf,
                                ?string  $rg = null)
    {
        $obj = $this->createDataObject($name);
        $obj->birthdate = $birthdate;
        $obj->customerType = $customerType;
        $obj->email = $email;
        $obj->gender = $gender;
        $obj->phone = $phone;
        $obj->address = $address;
        $obj->cpf = $cpf;
        $obj->rg = $rg;
        $export = $obj->export();

        $this->assertSame($name, $export['name'] ?? null);
        $this->assertSame($birthdate, $export['birthDate'] ?? null);
        $this->assertSame($customerType, $export['type'] ?? null);
        $this->assertSame($email, $export['email'] ?? null);
        $this->assertSame($gender, $export['gender'] ?? null);
        $this->assertSame($phone, $export['phone'] ?? null);
        $this->assertSame($address->address ?? null, $export['address'] ?? null);
        $this->assertSame($address->address2 ?? null, $export['address2'] ?? null);
        $this->assertSame($address->district ?? null, $export['district'] ?? null);
        $this->assertSame($address->city ?? null, $export['city'] ?? null);
        $this->assertSame($address->state ?? null, $export['state'] ?? null);
        $this->assertSame($address->country ?? null, $export['country'] ?? null);
        $this->assertSame($address->postalcode ?? null, $export['postalcode'] ?? null);
        $this->assertIsArray($export['documents'] ?? null);
        $this->assertIsArray($export['documents']['document'] ?? null);

        $cpfDocuments = array_values(array_filter(
            $export['documents']['document'],
            fn(array $document) => ($document['documentType'] ?? null) === 'CPF'
        ));

        $rgDocuments = array_values(array_filter(
            $export['documents']['document'],
            fn(array $document) => ($document['documentType'] ?? null) === 'RG'
        ));

        $this->assertCount(count($cpfDocuments), $cpfDocuments);
        $this->assertCount(count($rgDocuments), $rgDocuments);
        $this->assertSame($cpf, $cpfDocuments[0]['documentValue'] ?? null);
        $this->assertSame($rg, $rgDocuments[0]['documentValue'] ?? null);
    }

    public static function dataProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'null values' => [
                $faker->name(),
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ],
            'null values except address' => [
                $faker->name(),
                null,
                null,
                null,
                null,
                null,
                new Address(
                    $faker->streetAddress(),
                    null,
                    $faker->words(3, true),
                    $faker->city(),
                    $faker->stateAbbr(),
                    $faker->postcode(),
                    $faker->countryCode()
                ),
                null,
                null,
            ],
            'non-null values' => [
                $faker->name(),
                $faker->date(),
                FakerHelper::randomEnumValue(CustomerType::class),
                $faker->email(),
                FakerHelper::randomEnumValue(CustomerGender::class),
                $faker->phoneNumber(),
                new Address(
                    $faker->streetAddress(),
                    $faker->secondaryAddress(),
                    $faker->words(3, true),
                    $faker->city(),
                    $faker->stateAbbr(),
                    $faker->postcode(),
                    $faker->countryCode()
                ),
                $faker->cpf(),
                $faker->rg()
            ]
        ];
    }

    protected function createDataObject(string $name): AbstractData
    {
        return new class($name) extends AbstractData {

        };
    }
}
