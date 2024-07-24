<?php

namespace Vitorccs\Maxipago\Test\Entities\Sales;

use PHPUnit\Framework\Attributes\DataProvider;
use Vitorccs\Maxipago\Entities\PayTypes\AbstractPayType;
use Vitorccs\Maxipago\Entities\PayTypes\CreditCardPayType;
use Vitorccs\Maxipago\Entities\PayTypes\OnFilePayType;
use Vitorccs\Maxipago\Entities\Sales\AbstractSale;
use Vitorccs\Maxipago\Entities\Sales\CreditCardSale;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class CreditCardSaleTest extends AbstractSaleTest
{
    #[DataProvider('creditCardWithTokenSaleProvider')]
    public function test_credit_card_with_token_export(Payment       $payment,
                                                       string        $referenceNum,
                                                       OnFilePayType $onFilePayType)
    {
        $obj = $this->createSaleObject($onFilePayType, $payment, $referenceNum);
        $export = $obj->export();

        $this->assertArrayHasKey('transactionDetail', $export);
        $this->assertArrayHasKey('payType', $export['transactionDetail']);
        $this->assertArrayHasKey($onFilePayType->nodeName(), $export['transactionDetail']['payType']);

        $key = $onFilePayType->nodeName();
        $typeNode = $export['transactionDetail']['payType'][$key];

        $this->assertSame($onFilePayType->customerId, $typeNode['customerId']);
        $this->assertSame($onFilePayType->token, $typeNode['token']);
    }

    #[DataProvider('creditCardWithoutTokenSaleProvider')]
    public function test_credit_card_without_token_export(Payment           $payment,
                                                          string            $referenceNum,
                                                          CreditCardPayType $creditCardPayType)
    {
        $obj = $this->createSaleObject($creditCardPayType, $payment, $referenceNum);
        $export = $obj->export();

        $this->assertArrayHasKey('transactionDetail', $export);
        $this->assertArrayHasKey('payType', $export['transactionDetail']);
        $this->assertArrayHasKey($creditCardPayType->nodeName(), $export['transactionDetail']['payType']);

        $key = $creditCardPayType->nodeName();
        $typeNode = $export['transactionDetail']['payType'][$key];

        $this->assertSame($creditCardPayType->number, $typeNode['number']);
        $this->assertSame($creditCardPayType->expMonth, $typeNode['expMonth']);
        $this->assertSame($creditCardPayType->expYear, $typeNode['expYear']);
        $this->assertSame($creditCardPayType->cvvNumber, $typeNode['cvvNumber']);
    }

    public static function creditCardWithTokenSaleProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'with token' => [
                new Payment($faker->randomFloat()),
                $faker->uuid(),
                new OnFilePayType($faker->numberBetween(), $faker->word()),
            ]
        ];
    }

    public static function creditCardWithoutTokenSaleProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'without token' => [
                new Payment($faker->randomFloat()),
                $faker->uuid(),
                new CreditCardPayType($faker->creditCardNumber(), $faker->month(), $faker->year(), $faker->randomNumber())
            ]
        ];
    }

    protected function createSaleObject(AbstractPayType $payType,
                                        Payment         $payment,
                                        string          $referenceNum): AbstractSale
    {
        /** @var OnFilePayType|CreditCardPayType $payType */

        return new CreditCardSale($payType, $payment, $referenceNum);
    }

    protected function createPayTypeObject(): OnFilePayType
    {
        return new OnFilePayType(1, '');
    }
}
