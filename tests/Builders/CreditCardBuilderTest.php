<?php

namespace Vitorccs\Maxipago\Test\Builders;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Builders\CreditCardBuilder;
use Vitorccs\Maxipago\Entities\CreditCard;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class CreditCardBuilderTest extends TestCase
{
    #[DataProvider('requiredFieldsProvider')]
    public function test_create_required_fields(int        $customerId,
                                                string     $creditCardNumber,
                                                string|int $expirationMonth,
                                                int        $expirationYear)
    {
        $builder = CreditCardBuilder::create(
            $customerId,
            $creditCardNumber,
            $expirationMonth,
            $expirationYear
        );

        $customer = $builder->get();

        $this->assertInstanceOf(CreditCard::class, $customer);
        $this->assertSame($customerId, $customer->customerId);
        $this->assertSame($creditCardNumber, $customer->creditCardNumber);
        $expirationMonth < 10
            ? $this->assertSame('0' . $expirationMonth, $customer->expirationMonth)
            : $this->assertSame($expirationMonth, $customer->expirationMonth);
        $this->assertSame($expirationYear, $customer->expirationYear);

        $this->assertNull($customer->billingName);
        $this->assertNull($customer->billingEmail);
        $this->assertNull($customer->billingPhone);
        $this->assertNull($customer->billingAddress1);
        $this->assertNull($customer->billingAddress2);
        $this->assertNull($customer->billingCity);
        $this->assertNull($customer->billingState);
        $this->assertNull($customer->billingZip);
        $this->assertNull($customer->billingCountry);
    }

    #[DataProvider('optionalFieldsProvider')]
    public function test_create_optional_fields(int        $customerId,
                                                string     $creditCardNumber,
                                                string|int $expirationMonth,
                                                int        $expirationYear,
                                                string     $billingName,
                                                string     $billingEmail,
                                                string     $billingPhone,
                                                string     $billingAddress1,
                                                string     $billingAddress2,
                                                string     $billingCity,
                                                string     $billingState,
                                                string     $billingZip,
                                                string     $billingCountry)
    {
        $builder = CreditCardBuilder::create(
            $customerId,
            $creditCardNumber,
            $expirationMonth,
            $expirationYear
        );
        $builder->setBillingName($billingName);
        $builder->setBillingEmail($billingEmail);
        $builder->setBillingPhone($billingPhone);
        $builder->setBillingAddressFields(
            $billingAddress1,
            $billingAddress2,
            $billingCity,
            $billingState,
            $billingZip,
            $billingCountry
        );

        $customer = $builder->get();

        $this->assertInstanceOf(CreditCard::class, $customer);
        $this->assertSame($customerId, $customer->customerId);
        $this->assertSame($creditCardNumber, $customer->creditCardNumber);
        $expirationMonth < 10
            ? $this->assertSame('0' . $expirationMonth, $customer->expirationMonth)
            : $this->assertSame((string)$expirationMonth, $customer->expirationMonth);
        $this->assertSame($expirationYear, $customer->expirationYear);
        $this->assertSame($billingName, $customer->billingName);
        $this->assertSame($billingEmail, $customer->billingEmail);
        $this->assertSame($billingPhone, $customer->billingPhone);
        $this->assertSame($billingAddress1, $customer->billingAddress1);
        $this->assertSame($billingAddress2, $customer->billingAddress2);
        $this->assertSame($billingCity, $customer->billingCity);
        $this->assertSame($billingState, $customer->billingState);
        $this->assertSame($billingZip, $customer->billingZip);
        $this->assertSame($billingCountry, $customer->billingCountry);
    }

    public static function requiredFieldsProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'required (add zero padding)' => [
                $faker->numberBetween(),
                $faker->creditCardNumber,
                5,
                intval($faker->year('10 years'))
            ]
        ];
    }

    public static function optionalFieldsProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'required (no zero padding)' => [
                $faker->numberBetween(),
                $faker->creditCardNumber,
                10,
                intval($faker->year('10 years')),
                $faker->name(),
                $faker->email(),
                $faker->phoneNumber(),
                $faker->streetAddress(),
                $faker->secondaryAddress(),
                $faker->city(),
                $faker->stateAbbr(),
                $faker->postcode(),
                $faker->country()
            ]
        ];
    }
}
