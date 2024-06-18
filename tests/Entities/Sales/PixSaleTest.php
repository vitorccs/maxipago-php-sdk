<?php

namespace Vitorccs\Maxipago\Test\Entities\Sales;

use PHPUnit\Framework\Attributes\DataProvider;
use Vitorccs\Maxipago\Entities\PayTypes\AbstractPayType;
use Vitorccs\Maxipago\Entities\PayTypes\PixPayType;
use Vitorccs\Maxipago\Entities\Sales\AbstractSale;
use Vitorccs\Maxipago\Entities\Sales\PixSale;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;
use Vitorccs\Maxipago\Enums\Processor;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class PixSaleTest extends AbstractSaleTest
{
    #[DataProvider('pixSaleProvider')]
    public function test_pix_export(Payment    $payment,
                                    string     $referenceNum,
                                    int        $processorID,
                                    PixPayType $pixPayType)
    {
        $obj = $this->createSaleObject($pixPayType, $payment, $referenceNum, $processorID);
        $export = $obj->export();

        $this->assertArrayHasKey('transactionDetail', $export);
        $this->assertArrayHasKey('payType', $export['transactionDetail']);
        $this->assertArrayHasKey($pixPayType->nodeName(), $export['transactionDetail']['payType']);

        $key = $pixPayType->nodeName();
        $typeNode = $export['transactionDetail']['payType'][$key];

        $this->assertSame($pixPayType->expirationTime, $typeNode['expirationTime']);
        $this->assertSame($pixPayType->paymentInfo, $typeNode['paymentInfo']);
    }

    public static function pixSaleProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'required values' => [
                new Payment($faker->randomFloat()),
                $faker->uuid(),
                FakerHelper::randomEnumValue(Processor::class),
                new PixPayType($faker->randomNumber()),
            ],
            'optional values' => [
                new Payment($faker->randomFloat()),
                $faker->uuid(),
                FakerHelper::randomEnumValue(Processor::class),
                new PixPayType($faker->randomNumber(), $faker->paragraph(1))
            ]
        ];
    }

    protected function createSaleObject(AbstractPayType $payType,
                                        Payment         $payment,
                                        string          $referenceNum,
                                        int             $processorID): AbstractSale
    {
        /** @var PixPayType $payType */

        return new PixSale($payType, $payment, $referenceNum, $processorID);
    }

    protected function createPayTypeObject(): PixPayType
    {
        return new PixPayType(0);
    }
}
