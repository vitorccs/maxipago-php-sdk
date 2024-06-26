<?php

namespace Vitorccs\Maxipago\Test\Builders;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Builders\AbstractSaleBuilder;
use Vitorccs\Maxipago\Entities\PayTypes\AbstractPayType;
use Vitorccs\Maxipago\Entities\Sales\AbstractSale;
use Vitorccs\Maxipago\Entities\Sales\Sections\BillingData;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;
use Vitorccs\Maxipago\Entities\Sales\Sections\ShippingData;
use Vitorccs\Maxipago\Enums\Answer;
use Vitorccs\Maxipago\Enums\Processor;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class AbstractSaleBuilderTest extends TestCase
{
    #[DataProvider('requiredFieldsProvider')]
    public function test_create_required_fields(float  $chargeTotal,
                                                string $referenceNum)
    {
        $payType = self::createPayTypeObject();
        $sale = self::createSaleObject(
            $payType,
            new Payment($chargeTotal),
            $referenceNum
        );

        $saleBuilder = self::createSaleBuilderObject($sale, $payType);
        $saleData = $saleBuilder->get();

        $this->assertSame($chargeTotal, $saleData->payment->chargeTotal);
        $this->assertSame($referenceNum, $saleData->referenceNum);
        $this->assertNull($saleData->processorId);
        $this->assertNull($saleData->fraudCheck);
        $this->assertNull($saleData->ipAddress);
        $this->assertNull($saleData->customerIdExt);
        $this->assertNull($saleData->billing);
        $this->assertNull($saleData->shipping);
        $this->assertNull($saleData->payment->currencyCode);
        $this->assertNull($saleData->payment->softDescriptor);
        $this->assertNull($saleData->payment->shippingTotal);
    }

    #[DataProvider('optionalFieldsProvider')]
    public function test_create_optional_fields(float        $chargeTotal,
                                                string       $referenceNum,
                                                ?Processor   $processor,
                                                ?Answer      $fraudCheck,
                                                string       $ipAddress,
                                                string       $customerIdExt,
                                                string       $currencyCode,
                                                string       $softDescriptor,
                                                float        $shippingTotal,
                                                BillingData  $billing,
                                                ShippingData $shipping)
    {
        $payType = self::createPayTypeObject();
        $sale = self::createSaleObject(
            $payType,
            new Payment($chargeTotal),
            $referenceNum
        );

        $saleBuilder = self::createSaleBuilderObject($sale, $payType);
        $saleBuilder->setProcessorId($processor);
        $saleBuilder->setFraudCheck($fraudCheck);
        $saleBuilder->setIpAddress($ipAddress);
        $saleBuilder->setCustomerIdExt($customerIdExt);
        $saleBuilder->setPaymentCurrencyCode($currencyCode);
        $saleBuilder->setPaymentSoftDescriptor($softDescriptor);
        $saleBuilder->setPaymentShippingTotal($shippingTotal);
        $saleBuilder->setBilling($billing);
        $saleBuilder->setShipping($shipping);
        $saleData = $saleBuilder->get();

        $this->assertSame($chargeTotal, $saleData->payment->chargeTotal);
        $this->assertSame($referenceNum, $saleData->referenceNum);
        $this->assertSame($processor->value, $saleData->processorId);
        $this->assertSame($fraudCheck->value, $saleData->fraudCheck);
        $this->assertSame($ipAddress, $saleData->ipAddress);
        $this->assertSame($customerIdExt, $saleData->customerIdExt);
        $this->assertSame($currencyCode, $saleData->payment->currencyCode);
        $this->assertSame($softDescriptor, $saleData->payment->softDescriptor);
        $this->assertSame($shippingTotal, $saleData->payment->shippingTotal);
        $this->assertSame($billing, $saleData->billing);
        $this->assertSame($shipping, $saleData->shipping);
    }

    public static function createSaleBuilderObject(AbstractSale    $abstractSale,
                                                   AbstractPayType $abstractPayType): AbstractSaleBuilder
    {
        return new class($abstractSale, $abstractPayType) extends AbstractSaleBuilder {

        };
    }

    public static function createSaleObject(AbstractPayType $payType,
                                            Payment         $payment,
                                            string          $referenceNum): AbstractSale
    {
        return new class($payType, $payment, $referenceNum) extends AbstractSale {

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

        return [
            'required' => [
                $faker->randomFloat(0, 99999),
                $faker->uuid()
            ]
        ];
    }

    public static function optionalFieldsProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'optional' => [
                $faker->randomFloat(0, 99999),
                $faker->uuid(),
                FakerHelper::randomEnum(Processor::class),
                FakerHelper::randomEnum(Answer::class),
                $faker->ipv4(),
                $faker->cpf(),
                $faker->word(),
                $faker->word(),
                $faker->randomFloat(),
                new BillingData($faker->word()),
                new ShippingData($faker->word())
            ]
        ];
    }
}
