<?php

namespace Vitorccs\Maxipago\Test\Entities\Sales;

use PHPUnit\Framework\Attributes\DataProvider;
use Vitorccs\Maxipago\Entities\PayTypes\AbstractPayType;
use Vitorccs\Maxipago\Entities\PayTypes\PixPayType;
use Vitorccs\Maxipago\Entities\Sales\AbstractSale;
use Vitorccs\Maxipago\Entities\Sales\PixSale;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class PixSaleTest extends AbstractSaleTest
{
    #[DataProvider('pixSaleProvider')]
    public function test_pix_export(Payment    $payment,
                                    string     $referenceNum,
                                    PixPayType $pixPayType)
    {
        $obj = $this->createSaleObject($pixPayType, $payment, $referenceNum);
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
                new PixPayType($faker->randomNumber()),
            ],
            'optional values' => [
                new Payment($faker->randomFloat()),
                $faker->uuid(),
                new PixPayType($faker->randomNumber(), $faker->paragraph(1))
            ]
        ];
    }

    protected function createSaleObject(AbstractPayType $payType,
                                        Payment         $payment,
                                        string          $referenceNum): AbstractSale
    {
        /** @var PixPayType $payType */

        return new PixSale($payType, $payment, $referenceNum);
    }

    protected function createPayTypeObject(): PixPayType
    {
        return new PixPayType(0);
    }
}
