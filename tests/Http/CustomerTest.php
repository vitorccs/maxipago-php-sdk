<?php

namespace Vitorccs\Maxipago\Test\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Http\CustomerService;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;
use Vitorccs\Maxipago\Test\Shared\ResourceStubTrait;

class CustomerTest extends TestCase
{
    use ResourceStubTrait;

    #[DataProvider('createCustomerProvider')]
    public function test_create_customer(array   $payload,
                                         object  $expResponse,
                                         ?string $command)
    {
        $fmtPayload = [
            'request' => $payload
        ];

        $serviceStub = $this->setCustomerStubException(
            'postApi',
            [$fmtPayload, $command],
            $expResponse
        );

        $actResponse = $serviceStub->create($payload);

        $this->assertSame($expResponse, $actResponse);
    }

    public static function createCustomerProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'customer_sample' => [
                [],
                (object)[
                    'result' => (object)[
                        'customerId' => $faker->numberBetween(1),
                    ]
                ],
                'add-consumer'
            ]
        ];
    }

    private function setCustomerStubException(string $methodName,
                                              array  $args,
                                              object $responseBody): CustomerService
    {
        $mockBuilder = $this->getMockBuilder(CustomerService::class);

        /** @var MockObject&CustomerService $resourceStub */
        $resourceStub = $this->setStubResponse(
            $mockBuilder,
            $methodName,
            $args,
            $responseBody
        );

        return $resourceStub;
    }
}
