<?php

namespace Vitorccs\Maxipago\Test\Builders;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Builders\PixSaleBuilder;
use Vitorccs\Maxipago\Entities\Sales\PixSale;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class PixSaleBuilderTest extends TestCase
{
    #[DataProvider('requiredFieldsProvider')]
    public function test_create_required_fields(float  $chargeTotal,
                                                string $referenceNum,
                                                int    $expirationTime)
    {
        $builder = new PixSaleBuilder($chargeTotal, $referenceNum, $expirationTime);
        $pixSale = $builder->get();

        $this->assertInstanceOf(PixSale::class, $pixSale);
        $this->assertSame($chargeTotal, $pixSale->payment->chargeTotal);
        $this->assertSame($referenceNum, $pixSale->referenceNum);
        $this->assertSame($expirationTime, $pixSale->getPayType()->expirationTime);
        $this->assertNull($pixSale->getPayType()->paymentInfo);
    }

    #[DataProvider('optionalFieldsProvider')]
    public function test_optional_fields(float   $chargeTotal,
                                         string  $referenceNum,
                                         int     $expirationTime,
                                         ?string $paymentInfo)
    {
        $builder = new PixSaleBuilder($chargeTotal, $referenceNum, $expirationTime);
        $builder->setPixPaymentInfo($paymentInfo);
        $pixSale = $builder->get();

        $this->assertInstanceOf(PixSale::class, $pixSale);
        $this->assertSame($chargeTotal, $pixSale->payment->chargeTotal);
        $this->assertSame($referenceNum, $pixSale->referenceNum);
        $this->assertSame($expirationTime, $pixSale->getPayType()->expirationTime);
        $this->assertSame($paymentInfo, $pixSale->getPayType()->paymentInfo);
    }

    public static function requiredFieldsProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'null values' => [
                $faker->randomFloat(0, 99999),
                $faker->uuid(),
                $faker->numberBetween(0, 99999),
                null
            ]
        ];
    }

    public static function optionalFieldsProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'null values' => [
                $faker->randomFloat(0, 99999),
                $faker->uuid(),
                $faker->numberBetween(0, 99999),
                null
            ],
            'non-null values' => [
                $faker->randomFloat(0, 99999),
                $faker->uuid(),
                $faker->numberBetween(0, 99999),
                $faker->words(5, true)
            ]
        ];
    }
}
