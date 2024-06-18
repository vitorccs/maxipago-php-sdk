<?php

namespace Vitorccs\Maxipago\Test\Builders;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Builders\AbstractSaleBuilder;
use Vitorccs\Maxipago\Entities\PayTypes\AbstractPayType;
use Vitorccs\Maxipago\Entities\Sales\AbstractSale;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;
use Vitorccs\Maxipago\Enums\Processor;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class AbstractSaleBuilderTest extends TestCase
{
    #[DataProvider('requiredFieldsProvider')]
    public function test_create_required_fields(Processor $processor,
                                                float     $chargeTotal,
                                                string    $referenceNum)
    {
        $payType = self::createPayTypeObject();
        $sale = self::createSaleObject(
            $payType,
            new Payment($chargeTotal),
            $referenceNum,
            $processor->value
        );

        $saleBuilder = self::createSaleBuilderObject($sale, $payType);
        $saleData = $saleBuilder->get();

        $this->assertSame($processor->value, $saleData->processorID);
        $this->assertSame($chargeTotal, $saleData->payment->chargeTotal);
        $this->assertSame($referenceNum, $saleData->referenceNum);
        $this->assertNull($saleData->fraudCheck);
        $this->assertNull($saleData->ipAddress);
        $this->assertNull($saleData->customerIdExt);
        $this->assertNull($saleData->billing);
        $this->assertNull($saleData->shipping);
        $this->assertNull($saleData->payment->currencyCode);
        $this->assertNull($saleData->payment->softDescriptor);
        $this->assertNull($saleData->payment->shippingTotal);
    }

    public static function createSaleBuilderObject(AbstractSale    $abstractSale,
                                                   AbstractPayType $abstractPayType): AbstractSaleBuilder
    {
        return new class($abstractSale, $abstractPayType) extends AbstractSaleBuilder {

        };
    }

    public static function createSaleObject(AbstractPayType $payType,
                                            Payment         $payment,
                                            string          $referenceNum,
                                            int             $processorID): AbstractSale
    {
        return new class($payType, $payment, $referenceNum, $processorID) extends AbstractSale {

        };
    }

    public static function createPayTypeObject(): AbstractPayType
    {
        return new class() extends AbstractPayType {

            public function nodeName(): string
            {
                return 'nodeName';
            }
        };
    }

    public static function requiredFieldsProvider(): array
    {
        $faker = FakerHelper::get();
        $dataProvider = [];

        foreach (Processor::cases() as $case) {
            $processor = constant(Processor::class . "::" . $case->name);

            $dataProvider["Processor {$processor->value}"] = [
                $processor,
                $faker->randomFloat(0, 99999),
                $faker->uuid()
            ];
        }

        return $dataProvider;
    }
}
