<?php

namespace Vitorccs\Maxipago\Test\Http\Middlewares;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Vitorccs\Maxipago\Test\Shared\FakeHttpHelper;

trait FakeRequestTrait
{
    protected function getRequestBodyFromFakeRequest(callable $middleware,
                                                     array    $options): ?string
    {
        $container = [];

        /* @var Client $client */
        /* @var HandlerStack $handlerStack */
        list($client, , $handlerStack) = FakeHttpHelper::createClient(new Response(), $container);

        $handlerStack->push($middleware);
        $client->post('', $options);

        if (count($container) < 1) return null;

        /* @var RequestInterface $request */
        $request = $container[0]['request'];
        $body = $request->getBody();
        $body->rewind();

        return $body->getContents();
    }

    protected function getResponseBodyFromFakeRequest(callable $middleware,
                                                      Response $response): string
    {
        /* @var Client $client */
        /* @var HandlerStack $handlerStack */
        list($client, , $handlerStack) = FakeHttpHelper::createClient($response);
        $handlerStack->push($middleware);

        $response = $client->post('');

        return $response->getBody()->getContents();
    }
}
