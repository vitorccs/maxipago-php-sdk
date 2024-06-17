<?php

namespace Vitorccs\Maxipago\Test\Http\Middlewares;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Http\Middlewares\XmlRequestMiddleware;

class XmlRequestMiddlewareTest extends TestCase
{
    use FakeRequestTrait;

    #[DataProvider('authMiddlewareProvider')]
    public function test_create(array  $payload,
                                string $expectedXml)
    {
        $middleware = XmlRequestMiddleware::handle();
        $optionKey = XmlRequestMiddleware::OPTION_KEY;
        $requestBody = $this->getRequestBodyFromFakeRequest($middleware, [
            $optionKey => $payload
        ]);

        $this->assertNotNull($requestBody);
        $this->assertStringContainsString($expectedXml, $requestBody);
    }

    public static function authMiddlewareProvider(): array
    {
        return [
            'with root node' => [
                [
                    'root' => [
                        'null' => null,
                        'empty' => '',
                        'integer' => 1,
                        'string' => 'text',
                        'array' => ['a', 'b']
                    ]
                ],
                "<root><integer>1</integer><string>text</string><array>a</array><array>b</array></root>"
            ]
        ];
    }
}
