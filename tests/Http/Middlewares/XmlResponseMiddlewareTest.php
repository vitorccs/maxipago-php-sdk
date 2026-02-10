<?php

namespace Vitorccs\Maxipago\Test\Http\Middlewares;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Http\Middlewares\XmlResponseMiddleware;

class XmlResponseMiddlewareTest extends TestCase
{
    use FakeRequestTrait;

    #[DataProvider('validResponse')]
    #[DataProvider('invalidResponse')]
    public function test_create(Response $response,
                                string   $expectedJson)
    {
        $middleware = XmlResponseMiddleware::handle();
        $responseBody = $this->getResponseBodyFromFakeRequest($middleware, $response);

        $this->assertSame($expectedJson, $responseBody);
    }

    public static function validResponse(): array
    {
        return [
            'with root node' => [
                new Response(200, body: '<root><empty></empty><integer>1</integer><string>text</string><array>a</array><array>b</array></root>'),
                '{"empty":"","integer":"1","string":"text","array":["a","b"]}'
            ]
        ];
    }

    public static function invalidResponse(): array
    {
        return [
            'empty body' => [
                new Response(200, body: ''),
                'null'
            ],
            'plain text' => [
                new Response(200, body: 'content as plain text'),
                'null'
            ],
            'malformed xml' => [
                new Response(200, body: '<root><empty>malformed XML</root>'),
                'null'
            ]
        ];
    }
}
