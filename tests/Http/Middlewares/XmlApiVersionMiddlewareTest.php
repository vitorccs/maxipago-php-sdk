<?php

namespace Vitorccs\Maxipago\Test\Http\Middlewares;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Http\Middlewares\XmlApiVersionMiddleware;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class XmlApiVersionMiddlewareTest extends TestCase
{
    use FakeRequestTrait;

    #[DataProvider('versionMiddlewareProvider')]
    public function test_create(?string $version,
                                string  $payload,
                                string  $expectedXml)
    {
        $optionKey = XmlApiVersionMiddleware::OPTION_KEY;

        $middleware = XmlApiVersionMiddleware::handle();
        $requestBody = $this->getRequestBodyFromFakeRequest($middleware, [
            'body' => $payload,
            $optionKey => $version
        ]);

        $this->assertNotNull($requestBody);
        $this->assertStringContainsString($expectedXml, $requestBody);
    }

    public static function versionMiddlewareProvider(): array
    {
        $version = FakerHelper::get()->uuid();

        return [
            'with version' => [
                $version,
                '<root></root>',
                "<root><version>{$version}</version></root>"
            ],
            'no version set' => [
                null,
                '<root></root>',
                "<root></root>"
            ]
        ];
    }
}
