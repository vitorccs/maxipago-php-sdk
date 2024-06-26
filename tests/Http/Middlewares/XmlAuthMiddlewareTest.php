<?php

namespace Vitorccs\Maxipago\Test\Http\Middlewares;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Entities\Parameters;
use Vitorccs\Maxipago\Http\Middlewares\XmlAuthMiddleware;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class XmlAuthMiddlewareTest extends TestCase
{
    use FakeRequestTrait;

    #[DataProvider('authMiddlewareProvider')]
    public function test_create(Parameters $parameters,
                                string     $payload,
                                string     $expectedXml)
    {
        $middleware = XmlAuthMiddleware::handle($parameters);
        $requestBody = $this->getRequestBodyFromFakeRequest($middleware, [
            'body' => $payload
        ]);

        $this->assertNotNull($requestBody);
        $this->assertStringContainsString($expectedXml, $requestBody);
    }

    public static function authMiddlewareProvider(): array
    {
        $merchantId = FakerHelper::get()->uuid();
        $merchantKey = FakerHelper::get()->uuid();

        return [
            'with root node' => [
                new Parameters($merchantId, $merchantKey),
                '<root></root>',
                "<root><verification><merchantId>{$merchantId}</merchantId><merchantKey>{$merchantKey}</merchantKey></verification></root>"
            ]
        ];
    }
}
