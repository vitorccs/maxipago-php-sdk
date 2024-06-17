<?php

namespace Vitorccs\Maxipago\Test\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Http\QueryService;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;
use Vitorccs\Maxipago\Test\Shared\ResourceStubTrait;

class QueryServiceTest extends TestCase
{
    use ResourceStubTrait;

    #[DataProvider('orderIdProvider')]
    public function test_get_by_order_id(string  $orderId,
                                         array   $payload,
                                         object  $expResponse,
                                         array   $expRecords,
                                         ?string $command)
    {
        $serviceStub = $this->getQueryServiceStub('reportsApi', $payload, $expResponse, $command);
        $actResponse = $serviceStub->getByOrderId($orderId);

        $this->assertCount(count($expRecords), $actResponse);

        foreach ($actResponse as $i => $actRecord) {
            $this->assertSame((array)$expRecords[$i], (array)$actRecord);
        }
    }

    #[DataProvider('orderIdProvider')]
    public function test_get_last_by_order_id(string  $orderId,
                                              array   $payload,
                                              object  $expResponse,
                                              array   $expRecords,
                                              ?string $command)
    {
        $serviceStub = $this->getQueryServiceStub('reportsApi', $payload, $expResponse, $command);
        $actResponse = $serviceStub->getLastByOrderId($orderId);
        $firstRecord = count($expRecords) ? $expRecords[0] : null;

        is_null($firstRecord)
            ? $this->assertNull($actResponse)
            : $this->assertSame((array)$firstRecord, (array)$actResponse);
    }

    #[DataProvider('referenceNumberProvider')]
    public function test_get_by_reference_number(string  $referenceNum,
                                                 array   $payload,
                                                 object  $expResponse,
                                                 array   $expRecords,
                                                 ?string $command)
    {
        $serviceStub = $this->getQueryServiceStub('reportsApi', $payload, $expResponse, $command);
        $actResponse = $serviceStub->getByReferenceNumber($referenceNum);

        $this->assertCount(count($expRecords), $actResponse);

        foreach ($actResponse as $i => $actRecord) {
            $this->assertSame((array)$expRecords[$i], (array)$actRecord);
        }
    }

    #[DataProvider('transactionIdProvider')]
    public function test_get_by_transaction_id(string  $referenceNum,
                                               array   $payload,
                                               object  $expResponse,
                                               ?string $command)
    {
        $serviceStub = $this->getQueryServiceStub('reportsApi', $payload, $expResponse, $command);
        $actResponse = $serviceStub->getByTransactionId($referenceNum);
        $expResponseRecord = $expResponse->result->records->record ?? null;

        $this->assertSame($expResponseRecord, $actResponse);
    }

    public static function orderIdProvider(): array
    {
        $faker = FakerHelper::get();
        $orderId = $faker->uuid();

        return [
            'no records' => [
                $orderId,
                [
                    'request' => [
                        'filterOptions' => [
                            'orderId' => $orderId
                        ]
                    ]
                ],
                self::noOrdersResponseSample(),
                [],
                'transactionDetailReport'
            ],
            'one record' => [
                $orderId,
                [
                    'request' => [
                        'filterOptions' => [
                            'orderId' => $orderId
                        ]
                    ]
                ],
                self::singleOrderResponseSample(),
                [
                    static::fakeOrderGenerator('A')
                ],
                'transactionDetailReport'
            ],
            'multiple records' => [
                $orderId,
                [
                    'request' => [
                        'filterOptions' => [
                            'orderId' => $orderId
                        ]
                    ]
                ],
                self::multipleOrderResponseSample(),
                [
                    static::fakeOrderGenerator('A'),
                    static::fakeOrderGenerator('B'),
                    static::fakeOrderGenerator('C'),
                ],
                'transactionDetailReport'
            ],

        ];
    }

    public static function referenceNumberProvider(): array
    {
        $faker = FakerHelper::get();
        $referenceNumber = $faker->uuid();

        return [
            'no records' => [
                $referenceNumber,
                [
                    'request' => [
                        'filterOptions' => [
                            'referenceNum' => $referenceNumber
                        ]
                    ]
                ],
                self::noOrdersResponseSample(),
                [],
                'transactionDetailReport'
            ],
            'one record' => [
                $referenceNumber,
                [
                    'request' => [
                        'filterOptions' => [
                            'referenceNum' => $referenceNumber
                        ]
                    ]
                ],
                self::singleOrderResponseSample(),
                [
                    static::fakeOrderGenerator('A')
                ],
                'transactionDetailReport'
            ],
            'multiple records' => [
                $referenceNumber,
                [
                    'request' => [
                        'filterOptions' => [
                            'referenceNum' => $referenceNumber
                        ]
                    ]
                ],
                self::multipleOrderResponseSample(),
                [
                    static::fakeOrderGenerator('A'),
                    static::fakeOrderGenerator('B'),
                    static::fakeOrderGenerator('C'),
                ],
                'transactionDetailReport'
            ]
        ];
    }

    public static function transactionIdProvider(): array
    {
        $faker = FakerHelper::get();
        $transactionId = $faker->uuid();

        return [
            'order_sample' => [
                $transactionId,
                [
                    'request' => [
                        'filterOptions' => [
                            'transactionId' => $transactionId
                        ]
                    ]
                ],
                self::singleOrderResponseSample(),
                'transactionDetailReport'
            ]
        ];
    }

    private function getQueryServiceStub(string  $methodName,
                                         array   $payload,
                                         object  $responseBody,
                                         ?string $command = null): QueryService
    {
        $mockBuilder = $this->getMockBuilder(QueryService::class);

        /** @var MockObject&QueryService $resourceStub */
        $resourceStub = $this->setStubResponse(
            $mockBuilder,
            $methodName,
            $payload,
            $responseBody,
            $command
        );

        return $resourceStub;
    }

    private static function noOrdersResponseSample(): object
    {
        $faker = FakerHelper::get();

        return (object)[
            'result' => (object)[
                'records' => static::fakeOrderGenerator(),
            ]
        ];
    }

    private static function singleOrderResponseSample(): object
    {
        $faker = FakerHelper::get();

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
                'records' => (object) [
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
}
