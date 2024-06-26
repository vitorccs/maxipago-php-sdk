<?php

namespace Vitorccs\Maxipago\Test\Entities;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Entities\Parameters;
use Vitorccs\Maxipago\Test\Shared\EnvHelper;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class ParametersTest extends TestCase
{
    protected function setUp(): void
    {
        EnvHelper::resetEnv();
    }

    #[DataProvider('argumentsProvider')]
    public function test_from_constructor(?string $merchantId = null,
                                          ?string $merchantKey = null,
                                          ?bool   $sandbox = null,
                                          ?int    $timeout = null)
    {

        $parameters = new Parameters(
            $merchantId,
            $merchantKey,
            $sandbox,
            $timeout
        );

        $this->assertSame($merchantId, $parameters->getMerchantId());
        $this->assertSame($merchantKey, $parameters->getMerchantKey());
        $this->assertSame($sandbox, $parameters->getSandbox());
        $this->assertSame($timeout, $parameters->getTimeout());
    }

    #[DataProvider('argumentsProvider')]
    public function test_default_value(?string $tenantId = null,
                                       ?string $apiKey = null,
                                       ?bool   $sandbox = null,
                                       ?int    $timeout = null)
    {
        $parameters = new Parameters(
            $tenantId,
            $apiKey,
        );

        $this->assertSame(Parameters::DEFAULT_SANDBOX, $parameters->getSandbox());
        $this->assertSame(Parameters::DEFAULT_TIMEOUT, $parameters->getTimeout());
    }

    #[DataProvider('argumentsProvider')]
    public function test_from_env(?string $merchantId = null,
                                  ?string $merchantKey = null,
                                  ?bool   $sandbox = null,
                                  ?int    $timeout = null)
    {
        $keyValues = [
            Parameters::MAXIPAGO_MERCHANT_ID => $merchantId,
            Parameters::MAXIPAGO_MERCHANT_KEY => $merchantKey,
            Parameters::MAXIPAGO_SANDBOX => $sandbox,
            Parameters::MAXIPAGO_TIMEOUT => $timeout
        ];

        EnvHelper::setEnv($keyValues);

        $parameters = new Parameters();

        $this->assertSame($merchantId, $parameters->getMerchantId());
        $this->assertSame($merchantKey, $parameters->getMerchantKey());
        $this->assertSame($sandbox, $parameters->getSandbox());
        $this->assertSame($timeout, $parameters->getTimeout());
    }

    public static function argumentsProvider(): array
    {
        return [
            'sandbox enabled' => [
                FakerHelper::get()->uuid(),
                FakerHelper::get()->uuid(),
                true,
                FakerHelper::get()->numberBetween(1)
            ],
            'sandbox disabled' => [
                FakerHelper::get()->uuid(),
                FakerHelper::get()->uuid(),
                false,
                FakerHelper::get()->numberBetween(1)
            ]
        ];
    }
}
