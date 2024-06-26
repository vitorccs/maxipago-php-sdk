<?php

namespace Vitorccs\Maxipago\Test\Http;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Vitorccs\Maxipago\Constants\Config;
use Vitorccs\Maxipago\Exceptions\MaxipagoRequestException;
use Vitorccs\Maxipago\Exceptions\MaxipagoValidationException;
use Vitorccs\Maxipago\Http\Api;
use Vitorccs\Maxipago\Test\Shared\FakeHttpHelper;

class ApiTest extends TestCase
{
    #[DataProvider('successRequestProvider')]
    public function test_success_request(Response $response)
    {
        $container = [];
        list($client) = FakeHttpHelper::createClient($response, $container);

        $api = new Api($client, 'my_root');
        $responseData = $api->postRequest('my_endpoint', [], 'my_command', 'v1.0.0');

        /* @var RequestInterface $request */
        $request = $container[0]['request'];
        $options = $container[0]['options'];

        $xmlBodyKey = Config::OPTION_XML_BODY;
        $xmlApiVersionKey = Config::OPTION_XML_API_VERSION;

        $this->assertIsObject($responseData);
        $this->assertSame('7951e553-70a8-4ba8-8897-db793537abb6', $responseData->orderID ?? null);
        $this->assertSame('my_endpoint', $request->getUri()->getPath());
        $this->assertSame('POST', $request->getMethod());
        $this->assertArrayHasKey($xmlBodyKey, $options);
        $this->assertArrayHasKey($xmlApiVersionKey, $options);
        $this->assertArrayHasKey('my_root', $options[$xmlBodyKey]);
        $this->assertArrayHasKey('command', $options[$xmlBodyKey]['my_root']);
        $this->assertSame('my_command', $options[$xmlBodyKey]['my_root']['command']);
    }

    #[DataProvider('invalidResponseBodyProvider')]
    public function test_invalid_response_body(Response $response)
    {
        list($client) = FakeHttpHelper::createClient($response);

        $api = new Api($client, '');
        $responseData = $api->postRequest('', []);

        $this->assertNull($responseData);
    }

    #[DataProvider('validationExceptionProvider')]
    public function test_validation_exception(Response $response)
    {
        $this->expectException(MaxipagoValidationException::class);
        $this->expectExceptionCode($response->getStatusCode());
        $this->expectExceptionMessage('description');

        list($client) = FakeHttpHelper::createClient($response);

        $api = new Api($client, '');
        $api->postRequest('', []);
    }

    #[DataProvider('requestExceptionProvider')]
    public function test_request_exception(Response $response)
    {
        $this->expectException(MaxipagoRequestException::class);
        $this->expectExceptionCode($response->getStatusCode());

        list($client) = FakeHttpHelper::createClient($response);

        $api = new Api($client, '');
        $api->postRequest('', []);
    }

    public static function successRequestProvider(): array
    {
        $responses = [];

        for ($i = 200; $i < 300; $i++) {
            $responses["HTTP $i Status"] = [
                new Response($i, body: '{"orderID":"7951e553-70a8-4ba8-8897-db793537abb6"}')
            ];
        }

        return $responses;
    }

    public static function invalidResponseBodyProvider(): array
    {
        return [
            'invalid body' => [
                new Response(200, body: 'cannot_json_decode'),
            ],
            'empty body' => [
                new Response(200, body: ''),
            ],
            'null body' => [
                new Response(200, body: null),
            ]
        ];
    }

    public static function validationExceptionProvider(): array
    {
        return [
            'errorCode (string)' => [
                new Response(200, body: '{"errorCode":"1","errorMsg":"description"}')
            ],
            'errorCode (int) inside header' => [
                new Response(200, body: '{"header":{"errorCode":1,"errorMsg":"description"}}')
            ],
            'responseCode (string)' => [
                new Response(200, body: '{"responseCode":"1","responseMessage":"description"}')
            ],
            'responseCode (int) with errorMessage' => [
                new Response(200, body: '{"responseCode":1,"errorMessage":"description"}')
            ]
        ];
    }

    public static function requestExceptionProvider(): array
    {
        $responses = [];

        for ($i = 400; $i < 600; $i++) {
            $responses["HTTP $i Error"] = [
                new Response($i)
            ];
        }

        return $responses;
    }
}
