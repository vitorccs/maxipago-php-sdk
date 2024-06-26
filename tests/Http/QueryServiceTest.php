<?php

namespace Vitorccs\Maxipago\Test\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Exceptions\MaxipagoNotFoundException;
use Vitorccs\Maxipago\Http\QueryService;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;
use Vitorccs\Maxipago\Test\Shared\ResourceStubTrait;

class QueryServiceTest extends TestCase
{
    use ResourceStubTrait;

    #[DataProvider('orderIdProvider')]
    public function test_get_by_order_id(string $orderId,
                                         array  $payload,
                                         object $expResponse,
                                         array  $expRecords,
                                         string $command,
                                         bool   $checkSuccess)
    {
        $this->expectNotFoundException($checkSuccess, $expRecords);

        $serviceStub = $this->getQueryServiceStub(
            'reportsApi',
            [$payload, $command],
            $expResponse,
        );

        $actResponse = $serviceStub->getByOrderId($orderId, $checkSuccess);

        $this->assertCount(count($expRecords), $actResponse);

        foreach ($actResponse as $i => $actRecord) {
            $this->assertSame((array)$expRecords[$i], (array)$actRecord);
        }
    }

    #[DataProvider('orderIdProvider')]
    public function test_get_last_by_order_id(string $orderId,
                                              array  $payload,
                                              object $expResponse,
                                              array  $expRecords,
                                              string $command,
                                              bool   $checkSuccess)
    {
        $this->expectNotFoundException($checkSuccess, $expRecords);

        $serviceStub = $this->getQueryServiceStub(
            'reportsApi',
            [$payload, $command],
            $expResponse
        );

        $actResponse = $serviceStub->getLastByOrderId($orderId, $checkSuccess);
        $firstRecord = count($expRecords) ? $expRecords[0] : null;

        is_null($firstRecord)
            ? $this->assertNull($actResponse)
            : $this->assertSame((array)$firstRecord, (array)$actResponse);
    }

    #[DataProvider('referenceNumberProvider')]
    public function test_get_by_reference_number(string $referenceNum,
                                                 array  $payload,
                                                 object $expResponse,
                                                 array  $expRecords,
                                                 string $command,
                                                 bool   $checkSuccess)
    {
        $this->expectNotFoundException($checkSuccess, $expRecords);

        $serviceStub = $this->getQueryServiceStub(
            'reportsApi',
            [$payload, $command],
            $expResponse
        );

        $actResponse = $serviceStub->getByReferenceNumber($referenceNum, $checkSuccess);

        $this->assertCount(count($expRecords), $actResponse);

        foreach ($actResponse as $i => $actRecord) {
            $this->assertSame((array)$expRecords[$i], (array)$actRecord);
        }
    }

    #[DataProvider('transactionIdProvider')]
    public function test_get_by_transaction_id(string $referenceNum,
                                               array  $payload,
                                               object $expResponse,
                                               array  $expRecords,
                                               string $command,
                                               bool   $checkSuccess)
    {
        $this->expectNotFoundException($checkSuccess, $expRecords);

        $serviceStub = $this->getQueryServiceStub(
            'reportsApi',
            [$payload, $command],
            $expResponse
        );

        $actResponse = $serviceStub->getByTransactionId($referenceNum, $checkSuccess);
        $expResponseRecord = $expResponse->result->records->record ?? null;

        $this->assertSame($expResponseRecord, $actResponse);
    }

    public static function orderIdProvider(): array
    {
        $faker = FakerHelper::get();
        $orderId = $faker->uuid();
        $payload = [
            'request' => [
                'filterOptions' => [
                    'orderId' => $orderId
                ]
            ]
        ];

        return [
            'no records empty' => [
                $orderId,
                $payload,
                self::noOrdersResponseSample(),
                [],
                'transactionDetailReport',
                false
            ],
            'no records exception' => [
                $orderId,
                $payload,
                self::noOrdersResponseSample(),
                [],
                'transactionDetailReport',
                true
            ],
            'one record' => [
                $orderId,
                $payload,
                self::singleOrderResponseSample(),
                [
                    static::fakeOrderGenerator('A')
                ],
                'transactionDetailReport',
                true
            ],
            'multiple records' => [
                $orderId,
                $payload,
                self::multipleOrderResponseSample(),
                [
                    static::fakeOrderGenerator('A'),
                    static::fakeOrderGenerator('B'),
                    static::fakeOrderGenerator('C'),
                ],
                'transactionDetailReport',
                true
            ],

        ];
    }

    public static function referenceNumberProvider(): array
    {
        $faker = FakerHelper::get();
        $referenceNumber = $faker->uuid();
        $payload = [
            'request' => [
                'filterOptions' => [
                    'referenceNum' => $referenceNumber
                ]
            ]
        ];

        return [
            'no records empty' => [
                $referenceNumber,
                $payload,
                self::noOrdersResponseSample(),
                [],
                'transactionDetailReport',
                false
            ],
            'no records exception' => [
                $referenceNumber,
                $payload,
                self::noOrdersResponseSample(),
                [],
                'transactionDetailReport',
                true
            ],
            'one record' => [
                $referenceNumber,
                $payload,
                self::singleOrderResponseSample(),
                [
                    static::fakeOrderGenerator('A')
                ],
                'transactionDetailReport',
                true
            ],
            'multiple records' => [
                $referenceNumber,
                $payload,
                self::multipleOrderResponseSample(),
                [
                    static::fakeOrderGenerator('A'),
                    static::fakeOrderGenerator('B'),
                    static::fakeOrderGenerator('C'),
                ],
                'transactionDetailReport',
                true
            ]
        ];
    }

    public static function transactionIdProvider(): array
    {
        $faker = FakerHelper::get();
        $transactionId = $faker->uuid();
        $payload = [
            'request' => [
                'filterOptions' => [
                    'transactionId' => $transactionId
                ]
            ]
        ];

        return [
            'no records empty' => [
                $transactionId,
                $payload,
                self::noOrdersResponseSample(),
                [],
                'transactionDetailReport',
                false
            ],
            'no records exception' => [
                $transactionId,
                $payload,
                self::noOrdersResponseSample(),
                [],
                'transactionDetailReport',
                true
            ],
            'record' => [
                $transactionId,
                $payload,
                self::singleOrderResponseSample(),
                [
                    static::fakeOrderGenerator('A')
                ],
                'transactionDetailReport',
                true
            ]
        ];
    }

    private function getQueryServiceStub(string $methodName,
                                         array  $args,
                                         object $responseBody): QueryService
    {
        $mockBuilder = $this->getMockBuilder(QueryService::class);

        /** @var MockObject&QueryService $resourceStub */
        $resourceStub = $this->setStubResponse(
            $mockBuilder,
            $methodName,
            $args,
            $responseBody
        );

        return $resourceStub;
    }

    private static function noOrdersResponseSample(): object
    {
        return (object)[
            'result' => (object)[
                'records' => static::fakeOrderGenerator(),
            ]
        ];
    }

    private static function singleOrderResponseSample(): object
    {
        return (object)[
            'result' => (object)[
                'records' => (object)[
                    'record' => static::fakeOrderGenerator('A'),
                ]
            ]
        ];
    }

    private static function multipleOrderResponseSample(): object
    {
        return (object)[
            'result' => (object)[
                'records' => (object)[
                    'record' => [
                        static::fakeOrderGenerator('A'),
                        static::fakeOrderGenerator('B'),
                        static::fakeOrderGenerator('C'),
                    ]
                ]
            ]
        ];
    }

    private static function fakeOrderGenerator(?string $letter = null): object
    {
        if (is_null($letter)) {
            return (object)[];
        }

        return (object)[
            'orderID' => "{$letter}1",
            'referenceNum' => "{$letter}2",
            'transactionID' => "{$letter}3"
        ];
    }

    private function expectNotFoundException(bool  $checkSuccess,
                                             array $expRecords): void
    {
        $shouldThrowNotFoundException = $checkSuccess && empty($expRecords);

        if ($shouldThrowNotFoundException) {
            $this->expectException(MaxipagoNotFoundException::class);
            $this->expectExceptionMessage('Transaction not found');
        }
    }
}
