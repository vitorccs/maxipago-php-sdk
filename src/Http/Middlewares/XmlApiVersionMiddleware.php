<?php

namespace Vitorccs\Maxipago\Http\Middlewares;

use Psr\Http\Message\RequestInterface;
use Vitorccs\Maxipago\Constants\Config;

/**
 * Adds Maxipago XML Version to XML body
 *
 * Format:
 * <version>xxxx</version>
 */
class XmlApiVersionMiddleware
{
    const OPTION_KEY = Config::OPTION_XML_API_VERSION;

    public static function handle(): \Closure
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $apiVersion = $options[self::OPTION_KEY] ?? null;

                if (empty($apiVersion)) {
                    return $handler($request, $options);
                }

                $body = $request->getBody();
                $body->rewind();
                $contents = $body->getContents();
                $xml = simplexml_load_string($contents);

                $xml->addChild('version', $apiVersion);
                $contents = $xml->asXML();

                $body->rewind();
                $body->write($contents);
                $body->rewind();

                $request->withBody($body);

                return $handler($request, $options);
            };
        };
    }
}
