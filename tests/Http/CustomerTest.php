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
                                         int     $expCustomerId,
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

        $actCustomerId = $serviceStub->create($payload);

        $this->assertSame($expCustomerId, $actCustomerId);
    }

    #[DataProvider('createCreditCardProvider')]
    public function test_create_credit_card(array   $payload,
                                            object  $expResponse,
                                            string  $expToken,
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

        $actToken = $serviceStub->saveCard($payload);

        $this->assertSame($expToken, $actToken);
    }

    public static function createCustomerProvider(): array
    {
        $customerId = FakerHelper::get()->numberBetween(1);

        return [
            'customer_sample' => [
                [],
                (object)[
                    'result' => (object)[
                        'customerId' => $customerId,
                    ]
                ],
                $customerId,
                'add-consumer'
            ]
        ];
    }

    public static function createCreditCardProvider(): array
    {
        $token = FakerHelper::get()->word();

        return [
            'customer_sample' => [
                [],
                (object)[
                    'result' => (object)[
                        'token' => $token,
                    ]
                ],
                $token,
                'add-card-onfile'
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
