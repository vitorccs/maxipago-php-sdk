<?php

namespace Vitorccs\Maxipago\Test\Http\Middlewares;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Http\Middlewares\XmlResponseMiddleware;

class XmlResponseMiddlewareTest extends TestCase
{
    use FakeRequestTrait;

    #[DataProvider('authMiddlewareProvider')]
    public function test_create(Response $response,
                                string   $expectedJson)
    {
        $middleware = XmlResponseMiddleware::handle();
        $responseBody = $this->getResponseBodyFromFakeRequest($middleware, $response);

        $this->assertSame($expectedJson, $responseBody);
    }

    public static function authMiddlewareProvider(): array
    {
        return [
            'with root node' => [
                new Response(200, body: '<root><empty></empty><integer>1</integer><string>text</string><array>a</array><array>b</array></root>'),
                '{"empty":"","integer":"1","string":"text","array":["a","b"]}'
            ]
        ];
    }
}
