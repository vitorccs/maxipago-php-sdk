<?php

namespace Vitorccs\Maxipago\Test\Shared;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

class FakeHttpHelper
{
    public static function createClient(Response $response,
                                        array    &$container = []): array
    {
        $mockHandler = self::createMockHandler($response);
        $handlerStack = HandlerStack::create($mockHandler);
        $handlerStack->push(Middleware::history($container));

        $fakeClient = new Client([
            'handler' => $handlerStack,
            'headers' => [
                'Content-Type' => 'application/xml'
            ]
        ]);

        return [$fakeClient, $mockHandler, $handlerStack];
    }

    public static function createMockHandler(?Response $response): MockHandler
    {
        $mockHandler = new MockHandler();

        if (!is_null($response)) {
            $mockHandler->append($response);
        }

        return $mockHandler;
    }
}
