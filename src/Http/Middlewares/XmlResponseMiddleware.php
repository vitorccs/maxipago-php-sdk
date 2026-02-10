<?php

namespace Vitorccs\Maxipago\Http\Middlewares;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Vitorccs\Maxipago\Converters\SymfonyXmlConverter;
use Vitorccs\Maxipago\Interfaces\XmlConverter;

/**
 * Converts Response Body from string XML to string JSON, so
 * it can easily be decoded by Guzzle as an PHP object
 */
class XmlResponseMiddleware
{
    const HEADER_CONTENT_TYPE = 'application/json';

    public static function handle(?XmlConverter $converter = null): \Closure
    {
        $converter = $converter ?? new SymfonyXmlConverter();

        return function (callable $handler) use ($converter) {
            return function (RequestInterface $request, array $options) use ($handler, $converter) {
                $promise = $handler($request, $options);
                return $promise->then(
                    function (ResponseInterface $response) use ($converter) {
                        $body = $response->getBody();
                        $body->rewind();
                        $xmlContent = $body->getContents();

                        $arrayContent = $converter->decodeArray($xmlContent);
                        $jsonContent = json_encode($arrayContent);

                        return $response->withHeader('Content-Type', self::HEADER_CONTENT_TYPE)
                            ->withBody(Utils::streamFor($jsonContent));
                    }
                );
            };
        };
    }
}
