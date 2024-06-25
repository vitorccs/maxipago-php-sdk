<?php

namespace Vitorccs\Maxipago\Test\Builders;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Builders\BoletoSaleBuilder;
use Vitorccs\Maxipago\Entities\PayTypes\BoletoFields;
use Vitorccs\Maxipago\Entities\PayTypes\BoletoPayType;
use Vitorccs\Maxipago\Entities\Sales\BoletoSale;
use Vitorccs\Maxipago\Enums\BoletoChargeType;
use Vitorccs\Maxipago\Enums\Processor;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class BoletoSaleBuilderTest extends TestCase
{
    #[DataProvider('requiredFieldsProvider')]
    public function test_create_required_fields(Processor        $processor,
                                                float            $chargeTotal,
                                                string           $referenceNum,
                                                \Datetime|string $expirationDate)
    {
        $builder = new BoletoSaleBuilder($processor, $chargeTotal, $referenceNum, $expirationDate);
        $boletoSale = $builder->get();

        $this->assertInstanceOf(BoletoSale::class, $boletoSale);
        $this->assertSame($processor->value, $boletoSale->processorID);
        $this->assertSame($chargeTotal, $boletoSale->payment->chargeTotal);
        $this->assertSame($referenceNum, $boletoSale->referenceNum);
        is_string($expirationDate)
            ? $this->assertSame($expirationDate, $boletoSale->getPayType()->expirationDate)
            : $this->assertSame($expirationDate->format('Y-m-d'), $boletoSale->getPayType()->expirationDate);
        $this->assertNull($boletoSale->getPayType()->number);
        $this->assertNull($boletoSale->getPayType()->instructions);
        $this->assertNull($boletoSale->getPayType()->charge);
        $this->assertNull($boletoSale->getPayType()->interestRate);
        $this->assertNull($boletoSale->getPayType()->discount);
        $this->assertSame(BoletoPayType::DEFAULT_FORMAT, $boletoSale->getPayType()->format);
        $this->assertSame(BoletoPayType::DEFAULT_FIN_DOC_TYPE, $boletoSale->getPayType()->financialDocumentType);
    }

    #[DataProvider('optionalFieldsProvider')]
    public function test_optional_fields(Processor        $processor,
                                         float            $chargeTotal,
                                         string           $referenceNum,
                                         \Datetime|string $expirationDate,
                                         int              $number,
                                         string           $instructions,
                                         array            $charge,
                                         array            $discount,
                                         array            $interestRate)
    {
        $builder = new BoletoSaleBuilder($processor, $chargeTotal, $referenceNum, $expirationDate, $number);
        $builder->setInstructions($instructions);
        $builder->setCharge(...$charge);
        $builder->setDiscount(...$discount);
        $builder->setInterestRate(...$interestRate);
        $boletoSale = $builder->get();

        $this->assertInstanceOf(BoletoSale::class, $boletoSale);
        $this->assertSame($processor->value, $boletoSale->processorID);
        $this->assertSame($chargeTotal, $boletoSale->payment->chargeTotal);
        $this->assertSame($referenceNum, $boletoSale->referenceNum);
        $this->assertSame($number, $boletoSale->getPayType()->number);
        is_string($expirationDate)
            ? $this->assertSame($expirationDate, $boletoSale->getPayType()->expirationDate)
            : $this->assertSame($expirationDate->format('Y-m-d'), $boletoSale->getPayType()->expirationDate);
        $this->assertSame($instructions, $boletoSale->getPayType()->instructions);
        $this->assertSame(BoletoPayType::DEFAULT_FORMAT, $boletoSale->getPayType()->format);
        $this->assertSame(BoletoPayType::DEFAULT_FIN_DOC_TYPE, $boletoSale->getPayType()->financialDocumentType);

        $this->assertNotNull($boletoSale->getPayType()->charge);
        $this->assertSame($charge[0], $boletoSale->getPayType()->charge->date);
        $this->assertSame($charge[1]->value, $boletoSale->getPayType()->charge->type);
        $this->assertSame($charge[2], $boletoSale->getPayType()->charge->value);

        $this->assertNotNull($boletoSale->getPayType()->discount);
        $this->assertSame($discount[0], $boletoSale->getPayType()->discount->date);
        $this->assertSame($discount[1], $boletoSale->getPayType()->discount->value);
        $this->assertSame(BoletoChargeType::PERCENTUAL->value, $boletoSale->getPayType()->discount->type);

        $this->assertNotNull($boletoSale->getPayType()->interestRate);
        $this->assertSame($interestRate[0], $boletoSale->getPayType()->interestRate->date);
        $this->assertSame($interestRate[1], $boletoSale->getPayType()->interestRate->value);
        $this->assertSame(BoletoFields::DEF_FREQUENCY, $boletoSale->getPayType()->interestRate->frequency);
        $this->assertSame(BoletoChargeType::PERCENTUAL->value, $boletoSale->getPayType()->interestRate->type);
    }

    public static function requiredFieldsProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'string expirationDate' => [
                FakerHelper::randomEnum(Processor::class),
                $faker->randomFloat(0, 99999),
                $faker->uuid(),
                $faker->date()
            ],
            'Datetime expirationDate' => [
                FakerHelper::randomEnum(Processor::class),
                $faker->randomFloat(0, 99999),
                $faker->uuid(),
                $faker->dateTime()
            ],
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
                $faker->date(),
                $faker->numberBetween(0, 99999),
                $faker->sentence(),
                [$faker->date(), FakerHelper::randomEnum(BoletoChargeType::class), $faker->randomFloat()],
                [$faker->date(), $faker->randomFloat()],
                [$faker->date(), $faker->randomFloat(), $faker->word()],
            ]
        ];
    }
}
