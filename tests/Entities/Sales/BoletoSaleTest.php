<?php

namespace Vitorccs\Maxipago\Test\Entities\Sales;

use PHPUnit\Framework\Attributes\DataProvider;
use Vitorccs\Maxipago\Entities\PayTypes\AbstractPayType;
use Vitorccs\Maxipago\Entities\PayTypes\BoletoFields;
use Vitorccs\Maxipago\Entities\PayTypes\BoletoPayType;
use Vitorccs\Maxipago\Entities\Sales\AbstractSale;
use Vitorccs\Maxipago\Entities\Sales\BoletoSale;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;
use Vitorccs\Maxipago\Enums\BoletoChargeType;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class BoletoSaleTest extends AbstractSaleTest
{
    #[DataProvider('boletoSaleProvider')]
    public function test_boleto_export(Payment       $payment,
                                       string        $referenceNum,
                                       BoletoPayType $boletoPayType)
    {
        $obj = $this->createSaleObject($boletoPayType, $payment, $referenceNum);
        $export = $obj->export();

        $this->assertArrayHasKey('transactionDetail', $export);
        $this->assertArrayHasKey('payType', $export['transactionDetail']);
        $this->assertArrayHasKey($boletoPayType->nodeName(), $export['transactionDetail']['payType']);

        $key = $boletoPayType->nodeName();
        $typeNode = $export['transactionDetail']['payType'][$key];

        $this->assertSame($boletoPayType->expirationDate, $typeNode['expirationDate']);
        $this->assertSame($boletoPayType->number, $typeNode['number']);
        $this->assertSame($boletoPayType->format, $typeNode['format']);
        $this->assertSame($boletoPayType->instructions, $typeNode['instructions']);
        $this->assertSame($boletoPayType->financialDocumentType, $typeNode['financialDocumentType']);
        $this->assertSame($boletoPayType->charge?->export(), $typeNode['charge']);
        $this->assertSame($boletoPayType->interestRate?->export(), $typeNode['interestRate']);
        $this->assertSame($boletoPayType->discount?->export(), $typeNode['discount']);
    }

    public static function boletoSaleProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'required values' => [
                new Payment($faker->randomFloat()),
                $faker->uuid(),
                new BoletoPayType(
                    $faker->date()
                ),
            ],
            'optional values' => [
                new Payment($faker->randomFloat()),
                $faker->uuid(),
                new BoletoPayType(
                    $faker->date('d-m-Y'),
                    $faker->randomNumber(),
                    new BoletoFields($faker->date(), FakerHelper::randomEnumValue(BoletoChargeType::class), $faker->randomFloat()),
                    new BoletoFields($faker->date(), FakerHelper::randomEnumValue(BoletoChargeType::class), $faker->randomFloat(), false),
                    new BoletoFields($faker->date(), FakerHelper::randomEnumValue(BoletoChargeType::class), $faker->randomFloat(), true),
                    $faker->word(),
                    $faker->word(),
                    $faker->sentence(),
                ),
            ]
        ];
    }

    protected function createSaleObject(AbstractPayType $payType,
                                        Payment         $payment,
                                        string          $referenceNum): AbstractSale
    {
        /** @var BoletoPayType $payType */

        return new BoletoSale($payType, $payment, $referenceNum);
    }

    protected function createPayTypeObject(): BoletoPayType
    {
        return new BoletoPayType('31-01-2024');
    }
}
