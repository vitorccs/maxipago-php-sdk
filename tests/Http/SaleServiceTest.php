<?php

namespace Vitorccs\Maxipago\Test\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Entities\PayTypes\BoletoPayType;
use Vitorccs\Maxipago\Entities\PayTypes\CreditCardPayType;
use Vitorccs\Maxipago\Entities\PayTypes\OnFilePayType;
use Vitorccs\Maxipago\Entities\PayTypes\PixPayType;
use Vitorccs\Maxipago\Entities\Sales\AbstractSale;
use Vitorccs\Maxipago\Entities\Sales\BoletoSale;
use Vitorccs\Maxipago\Entities\Sales\CreditCardSale;
use Vitorccs\Maxipago\Entities\Sales\PixSale;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;
use Vitorccs\Maxipago\Exceptions\MaxipagoProcessorException;
use Vitorccs\Maxipago\Exceptions\MaxipagoValidationException;
use Vitorccs\Maxipago\Http\SaleService;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;
use Vitorccs\Maxipago\Test\Shared\ResourceStubTrait;

class SaleServiceTest extends TestCase
{
    use ResourceStubTrait;

    #[DataProvider('createSaleProvider')]
    public function test_create_sale(string       $methodName,
                                     AbstractSale $payload)
    {
        $mockBuilder = $this->getMockBuilder(SaleService::class);

        $stub = $mockBuilder
            ->disableOriginalConstructor()
            ->onlyMethods([$methodName])
            ->getMock();

        $stub->expects($this->once())
            ->method($methodName)
            ->with($payload)
            ->willReturn(new \stdClass());

        $actResponse = $stub->createSale($payload);

        $this->assertIsObject($actResponse);
    }

    #[DataProvider('createBoletoSaleProvider')]
    public function test_create_boleto(array   $payload,
                                       object  $expResponse,
                                       ?string $command)
    {
        $fmtPayload = [
            'order' => [
                'sale' => $payload
            ]
        ];

        $serviceStub = $this->setTransactionStubResponse(
            'postXml',
            [$fmtPayload, $command],
            $expResponse
        );

        $actResponse = $serviceStub->createBoletoSale($payload);

        $this->assertSame($expResponse, $actResponse);
    }

    #[DataProvider('createCreditCardSaleProvider')]
    public function test_create_credit_card(array   $payload,
                                            object  $expResponse,
                                            ?string $command)
    {
        $fmtPayload = [
            'order' => [
                'sale' => $payload
            ]
        ];

        $serviceStub = $this->setTransactionStubResponse(
            'postXml',
            [$fmtPayload, $command],
            $expResponse
        );

        $actResponse = $serviceStub->createCreditCardSale($payload);

        $this->assertSame($expResponse, $actResponse);
    }

    #[DataProvider('createPixSaleProvider')]
    public function test_create_pix(array   $payload,
                                    object  $expResponse,
                                    ?string $command)
    {
        $fmtPayload = [
            'order' => [
                'sale' => $payload
            ]
        ];

        $serviceStub = $this->setTransactionStubResponse(
            'postXml',
            [$fmtPayload, $command],
            $expResponse
        );

        $actResponse = $serviceStub->createPixSale($payload);

        $this->assertSame($expResponse, $actResponse);
    }

    #[DataProvider('processorExceptionProvider')]
    #[DataProvider('validationExceptionProvider')]
    public function test_create_sale_exception(AbstractSale                $payload,
                                               MaxipagoValidationException $exception,
                                               string                      $expectedClass,
                                               bool                        $checkSuccess)
    {
        $shouldThrowException = $expectedClass === MaxipagoValidationException::class
            || $checkSuccess;
        $actException = null;
        $actResponse = null;

        try {
            $serviceStub = $this->setTransactionStubException('postXml', $exception);
            $actResponse = $serviceStub->createSale($payload, $checkSuccess);
        } catch (MaxipagoValidationException|MaxipagoProcessorException $e) {
            $actException = $e;
        }

        if ($shouldThrowException) {
            $this->assertInstanceOf($expectedClass, $actException);
            $this->assertSame($exception->getCode(), $actException?->getCode());
            $this->assertSame($exception->getMessage(), $actException?->getMessage());
            $this->assertSame($exception->getResponseCode(), $actException?->getResponseCode());
            $this->assertSame($exception->getResponseBody(), $actException->getResponseBody());
            $this->assertSame($exception->getHttpCode(), $actException->getHttpCode());
        } else {
            $this->assertNull($actException);
            $this->assertIsObject($actResponse);
        }
    }

    #[DataProvider('cancelSaleProvider')]
    public function test_cancel_sale(string|int $transactionId,
                                     object     $expResponse,
                                     ?string    $command)
    {
        $fmtPayload = [
            'order' => [
                'void' => [
                    'transactionID' => $transactionId
                ]
            ]
        ];

        $serviceStub = $this->setTransactionStubResponse(
            'postXml',
            [$fmtPayload, $command],
            $expResponse
        );

        $actResponse = $serviceStub->cancelSale($transactionId);

        $this->assertSame($expResponse, $actResponse);
    }

    #[DataProvider('refundSaleProvider')]
    public function test_refund_sale(string     $orderId,
                                     string|int $referenceNum,
                                     float      $chargeTotal,
                                     object     $expResponse,
                                     ?string    $command)
    {
        $fmtPayload = [
            'order' => [
                'return' => [
                    'orderID' => $orderId,
                    'referenceNum' => $referenceNum,
                    'payment' => [
                        'chargeTotal' => $chargeTotal
                    ]
                ]
            ]
        ];

        $serviceStub = $this->setTransactionStubResponse(
            'postXml',
            [$fmtPayload, $command],
            $expResponse
        );

        $actResponse = $serviceStub->refundSale($orderId, $referenceNum, $chargeTotal);

        $this->assertSame($expResponse, $actResponse);
    }

    public static function createSaleProvider(): array
    {
        return [
            'pix' => [
                'createPixSale',
                self::pixSale()
            ],
            'boleto' => [
                'createBoletoSale',
                self::boletoSale(),
            ],
            'credit_card with token' => [
                'createCreditCardSale',
                self::creditCardTokenSale(),
            ],
            'credit_card without token' => [
                'createCreditCardSale',
                self::creditCardNoTokenSale(),
            ]
        ];
    }

    public static function createPixSaleProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'pix_sample' => [
                [],
                (object)[
                    'orderID' => $faker->uuid(),
                    'referenceNum' => $faker->uuid()
                ],
                null
            ]
        ];
    }

    public static function createBoletoSaleProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'boleto_sample' => [
                [],
                (object)[
                    'orderID' => $faker->uuid(),
                    'referenceNum' => $faker->uuid()
                ],
                null
            ]
        ];
    }

    public static function createCreditCardSaleProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'credit_card_sample' => [
                [],
                (object)[
                    'orderID' => $faker->uuid(),
                    'referenceNum' => $faker->uuid()
                ],
                null
            ]
        ];
    }

    public static function processorExceptionProvider(): array
    {
        $faker = FakerHelper::get();

        $exception = new MaxipagoValidationException('description', 101, 202, 200, (object)[
            'orderID' => $faker->uuid(),
            'referenceNum' => $faker->uuid(),
            'transactionID' => $faker->uuid(),
            'processorCode' => $faker->numberBetween(1),
            'processorMessage' => $faker->sentence(),
        ]);

        return [
            'PIX - Processor (check)' => [
                self::pixSale(),
                $exception,
                MaxipagoProcessorException::class,
                true,
            ],
            'PIX - Processor (no check)' => [
                self::pixSale(),
                $exception,
                MaxipagoProcessorException::class,
                false,
            ],
            'Boleto - Processor (check)' => [
                static::boletoSale(),
                $exception,
                MaxipagoProcessorException::class,
                true,
            ],
            'Boleto - Processor (no check)' => [
                static::boletoSale(),
                $exception,
                MaxipagoProcessorException::class,
                false,
            ],
        ];
    }

    public static function validationExceptionProvider(): array
    {
        $faker = FakerHelper::get();

        $provider = [];

        $payloads = [
            'withOrderID' => [
                'orderID' => $faker->uuid(),
                'referenceNum' => null,
                'transactionID' => null,
                'processorCode' => $faker->numberBetween(1),
                'processorMessage' => $faker->sentence(),
            ],
            'withReferenceNum' => [
                'orderID' => null,
                'referenceNum' => $faker->uuid(),
                'transactionID' => null,
                'processorCode' => $faker->numberBetween(1),
                'processorMessage' => $faker->sentence(),
            ],
            'withTransaction' => [
                'orderID' => null,
                'referenceNum' => null,
                'transactionID' => $faker->uuid(),
                'processorCode' => $faker->numberBetween(1),
                'processorMessage' => $faker->sentence(),
            ],
            'allNull' => [
                'orderID' => null,
                'referenceNum' => null,
                'transactionID' => null,
                'processorCode' => $faker->numberBetween(1),
                'processorMessage' => $faker->sentence(),
            ],
            'allEmpty' => [
                'orderID' => '',
                'referenceNum' => '',
                'transactionID' => '',
                'processorCode' => $faker->numberBetween(1),
                'processorMessage' => $faker->sentence(),
            ]
        ];

        $checks = [
            'true' => true,
            'false' => false
        ];

        foreach ($payloads as $pKey => $pValue) {
            $exception = new MaxipagoValidationException('description', 101, 202, 200, (object)$pValue);

            foreach ($checks as $cKey => $cValue) {
                $key = sprintf("Payload (%s) - check (%s)", $pKey, $cKey);

                $provider["PIX $key"] = [
                    self::pixSale(),
                    $exception,
                    MaxipagoValidationException::class,
                    $cValue,
                ];
                $provider["Boleto $key"] = [
                    self::boletoSale(),
                    $exception,
                    MaxipagoValidationException::class,
                    $cValue,
                ];
                $provider["CreditCard $key"] = [
                    self::creditCardNoTokenSale(),
                    $exception,
                    MaxipagoValidationException::class,
                    $cValue,
                ];
                $provider["CreditCard Token $key"] = [
                    self::creditCardTokenSale(),
                    $exception,
                    MaxipagoValidationException::class,
                    $cValue,
                ];
            }
        }

        return $provider;
    }

    public static function cancelSaleProvider(): array
    {
        $faker = FakerHelper::get();
        $transactionId = $faker->numberBetween(1);

        return [
            'cancel_sample' => [
                $transactionId,
                (object)[
                    'transactionID' => $transactionId,
                    'responseMessage' => 'VOIDED'
                ],
                null
            ]
        ];
    }

    public static function refundSaleProvider(): array
    {
        $faker = FakerHelper::get();
        $orderId = $faker->uuid();
        $referenceNum = $faker->uuid();

        return [
            'cancel_sample' => [
                $orderId,
                $referenceNum,
                100.00,
                (object)[
                    'orderID' => $orderId,
                    'referenceNum' => $referenceNum
                ],
                null
            ]
        ];
    }

    private function setTransactionStubResponse(string $methodName,
                                                array  $args,
                                                object $responseBody): SaleService
    {
        $mockBuilder = $this->getMockBuilder(SaleService::class);

        /** @var MockObject&SaleService $resourceStub */
        $resourceStub = $this->setStubResponse(
            $mockBuilder,
            $methodName,
            $args,
            $responseBody
        );

        return $resourceStub;
    }

    private static function boletoSale(): BoletoSale
    {
        return new BoletoSale(new BoletoPayType(''), new Payment(0), '', 0);
    }

    private static function creditCardTokenSale(): CreditCardSale
    {
        return new CreditCardSale(new OnFilePayType(1, ''), new Payment(0), '');
    }

    private static function creditCardNoTokenSale(): CreditCardSale
    {
        return new CreditCardSale(new CreditCardPayType('', '', '', ''), new Payment(0), '');
    }

    private static function pixSale(): PixSale
    {
        return new PixSale(new PixPayType(0), new Payment(0), '', 0);
    }

    private function setTransactionStubException(string     $methodName,
                                                 \Exception $exception): SaleService
    {
        $mockBuilder = $this->getMockBuilder(SaleService::class);

        /** @var MockObject&SaleService $resourceStub */
        $resourceStub = $this->setStubException(
            $mockBuilder,
            $methodName,
            $exception,
        );

        return $resourceStub;
    }
}
