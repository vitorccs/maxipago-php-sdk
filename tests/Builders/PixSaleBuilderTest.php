<?php

namespace Vitorccs\Maxipago\Test\Builders;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Builders\PixSaleBuilder;
use Vitorccs\Maxipago\Entities\Sales\PixSale;
use Vitorccs\Maxipago\Enums\Processor;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class PixSaleBuilderTest extends TestCase
{
    #[DataProvider('requiredFieldsProvider')]
    public function test_create_required_fields(Processor $processor,
                                                float     $chargeTotal,
                                                string    $referenceNum,
                                                int       $expirationTime)
    {
        $pixSaleBuilder = new PixSaleBuilder($processor, $chargeTotal, $referenceNum, $expirationTime);
        $pixSale = $pixSaleBuilder->get();

        $this->assertInstanceOf(PixSale::class, $pixSale);
        $this->assertSame($processor->value, $pixSale->processorID);
        $this->assertSame($chargeTotal, $pixSale->payment->chargeTotal);
        $this->assertSame($referenceNum, $pixSale->referenceNum);
        $this->assertSame($expirationTime, $pixSale->getPayType()->expirationTime);
        $this->assertNull($pixSale->getPayType()->paymentInfo);
    }

    #[DataProvider('optionalFieldsProvider')]
    public function test_optional_fields(Processor $processor,
                                         float     $chargeTotal,
                                         string    $referenceNum,
                                         int       $expirationTime,
                                         ?string   $paymentInfo)
    {
        $pixSaleBuilder = new PixSaleBuilder($processor, $chargeTotal, $referenceNum, $expirationTime);
        $pixSaleBuilder->setPixPaymentInfo($paymentInfo);
        $pixSale = $pixSaleBuilder->get();

        $this->assertInstanceOf(PixSale::class, $pixSale);
        $this->assertSame($processor->value, $pixSale->processorID);
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
                FakerHelper::randomEnum(Processor::class),
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
                FakerHelper::randomEnum(Processor::class),
                $faker->randomFloat(0, 99999),
                $faker->uuid(),
                $faker->numberBetween(0, 99999),
                null
            ],
            'non-null values' => [
                FakerHelper::randomEnum(Processor::class),
                $faker->randomFloat(0, 99999),
                $faker->uuid(),
                $faker->numberBetween(0, 99999),
                $faker->words(5, true)
            ]
        ];
    }
}
