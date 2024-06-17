<?php

namespace Vitorccs\Maxipago\Test\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Entities\AbstractSale;
use Vitorccs\Maxipago\Entities\PixSale;
use Vitorccs\Maxipago\Entities\SaleSections\Payment;
use Vitorccs\Maxipago\Entities\SaleSections\PixPayType;
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

        $serviceStub = $this->setTransactionStubResponse('postXml', $fmtPayload, $expResponse, $command);
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

        $serviceStub = $this->setTransactionStubResponse('postXml', $fmtPayload, $expResponse, $command);
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

        $serviceStub = $this->setTransactionStubResponse('postXml', $fmtPayload, $expResponse, $command);
        $actResponse = $serviceStub->refundSale($orderId, $referenceNum, $chargeTotal);

        $this->assertSame($expResponse, $actResponse);
    }

    public static function createSaleProvider(): array
    {
        return [
            'pix' => [
                'createPixSale',
                new PixSale(new PixPayType(0), new Payment(0), '', 0)
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

    public static function processorExceptionProvider(): array
    {
        $faker = FakerHelper::get();

        $processorError = (object)[
            'orderID' => $faker->uuid(),
            'referenceNum' => $faker->uuid(),
            'transactionID' => $faker->uuid(),
            'processorCode' => $faker->numberBetween(1),
            'processorMessage' => $faker->sentence(),
        ];

        return [
            'PIX - Processor (success)' => [
                new PixSale(new PixPayType(0), new Payment(0), '', 0),
                new MaxipagoValidationException('description', 101, 202, 200, $processorError),
                MaxipagoProcessorException::class,
                true,
            ],
            'PIX - Processor (no success)' => [
                new PixSale(new PixPayType(0), new Payment(0), '', 0),
                new MaxipagoValidationException('description', 101, 202, 200, $processorError),
                MaxipagoProcessorException::class,
                false,
            ]
        ];
    }

    public static function validationExceptionProvider(): array
    {
        $faker = FakerHelper::get();


        $validationError = (object)[
            'orderID' => null,
            'referenceNum' => null,
            'transactionID' => $faker->uuid(),
            'processorCode' => $faker->numberBetween(1),
            'processorMessage' => $faker->sentence(),
        ];

        return [
            'PIX - Validation (success)' => [
                new PixSale(new PixPayType(0), new Payment(0), '', 0),
                new MaxipagoValidationException('description', 101, 202, 200, $validationError),
                MaxipagoValidationException::class,
                true,
            ],
            'PIX - Validation (no success)' => [
                new PixSale(new PixPayType(0), new Payment(0), '', 0),
                new MaxipagoValidationException('description', 101, 202, 200, $validationError),
                MaxipagoValidationException::class,
                false,
            ],
        ];
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

    public function setTransactionStubResponse(string  $methodName,
                                               mixed   $payload,
                                               object  $responseBody,
                                               ?string $command = null): SaleService
    {
        $mockBuilder = $this->getMockBuilder(SaleService::class);

        /** @var MockObject&SaleService $resourceStub */
        $resourceStub = $this->setStubResponse(
            $mockBuilder,
            $methodName,
            $payload,
            $responseBody,
            $command
        );

        return $resourceStub;
    }

    public function setTransactionStubException(string     $methodName,
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
