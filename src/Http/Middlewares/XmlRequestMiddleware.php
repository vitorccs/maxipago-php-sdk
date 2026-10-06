<?php

namespace Vitorccs\Maxipago\Http\Middlewares;

use Psr\Http\Message\RequestInterface;
use Vitorccs\Maxipago\Constants\Config;
use Vitorccs\Maxipago\Converters\SymfonyXmlConverter;
use Vitorccs\Maxipago\Interfaces\XmlConverter;

/**
 * Converts Request Body from array format to XML string
 */
class XmlRequestMiddleware
{
    const string OPTION_KEY = Config::OPTION_XML_BODY;

    public static function handle(?XmlConverter $converter = null): \Closure
    {
        $converter ??= new SymfonyXmlConverter();

        return fn(callable $handler) => function (RequestInterface $request, array $options) use ($handler, $converter) {
            $xmlPayload = $options[self::OPTION_KEY] ?? null;

            if (!is_array($xmlPayload)) {
                return $handler($request, $options);
            }

            $contents = $converter->encode($xmlPayload);

            $body = $request->getBody();
            $body->rewind();
            $body->write($contents);
            $body->rewind();

            $request->withHeader('Content-Type', Config::HEADER_CONTENT_TYPE)
                ->withBody($body);

            return $handler($request, $options);
        };
    }
}
