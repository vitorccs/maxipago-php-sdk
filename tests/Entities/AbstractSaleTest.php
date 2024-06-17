<?php

namespace Vitorccs\Maxipago\Test\Entities;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Entities\AbstractSale;
use Vitorccs\Maxipago\Entities\SaleSections\AbstractPayType;
use Vitorccs\Maxipago\Entities\SaleSections\BillingData;
use Vitorccs\Maxipago\Entities\SaleSections\Payment;
use Vitorccs\Maxipago\Entities\SaleSections\ShippingData;
use Vitorccs\Maxipago\Enums\Answer;
use Vitorccs\Maxipago\Enums\Processor;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class AbstractSaleTest extends TestCase
{
    #[DataProvider('saleProvider')]
    public function test_export(Payment       $payment,
                                string        $referenceNum,
                                int           $processorID,
                                ?string       $ipAddress,
                                ?string       $fraudCheck,
                                ?string       $customerIdExt,
                                ?BillingData  $billingData,
                                ?ShippingData $shippingData)
    {
        $payType = $this->createPayTypeObject();
        $obj = $this->createSaleObject($payType, $payment, $referenceNum, $processorID);
        $obj->ipAddress = $ipAddress;
        $obj->fraudCheck = $fraudCheck;
        $obj->customerIdExt = $customerIdExt;
        $obj->billing = $billingData;
        $obj->shipping = $shippingData;
        $export = $obj->export();

        $this->assertSame($processorID, $export['processorID']);
        $this->assertSame($ipAddress, $export['ipAddress']);
        $this->assertSame($fraudCheck, $export['fraudCheck']);
        $this->assertSame($customerIdExt, $export['customerIdExt']);
        $this->assertSame($billingData?->export(), $export['billing']);
        $this->assertSame($shippingData?->export(), $export['shipping']);
        $this->assertArrayHasKey('transactionDetail', $export);
        $this->assertArrayHasKey('payType', $export['transactionDetail']);
        $this->assertArrayHasKey($payType->nodeName(), $export['transactionDetail']['payType']);
    }

    public static function saleProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'required values' => [
                new Payment($faker->randomFloat()),
                $faker->uuid(),
                FakerHelper::randomEnumValue(Processor::class),
                null,
                null,
                null,
                null,
                null
            ],
            'optional values' => [
                new Payment($faker->randomFloat()),
                $faker->uuid(),
                FakerHelper::randomEnumValue(Processor::class),
                $faker->ipv4(),
                FakerHelper::randomEnumValue(Answer::class),
                $faker->uuid(),
                new BillingData($faker->name()),
                new ShippingData($faker->name()),
            ]
        ];
    }

    protected function createSaleObject(AbstractPayType $payType,
                                        Payment         $payment,
                                        string          $referenceNum,
                                        int             $processorID): AbstractSale
    {
        return new class($payType, $payment, $referenceNum, $processorID) extends AbstractSale {

        };
    }

    protected function createPayTypeObject(): AbstractPayType
    {
        return new class() extends AbstractPayType {
            public function nodeName(): string
            {
                return 'nodeName';
            }
        };
    }
}
