<?php

namespace Vitorccs\Maxipago\Http\Middlewares;

use Psr\Http\Message\RequestInterface;
use Vitorccs\Maxipago\Entities\Parameters;

/**
 * Adds Authentication data to XML body
 *
 * Format:
 * <verification>
 *   <merchantId>xxxx</merchantId>
 *   <merchantKey>xxxx</merchantKey>
 * </verification>
 */
class XmlAuthMiddleware
{
    public static function handle(Parameters $parameters): \Closure
    {
        return function (callable $handler) use ($parameters) {
            return function (RequestInterface $request, array $options) use ($handler, $parameters) {
                $body = $request->getBody();
                $body->rewind();
                $contents = $body->getContents();
                $xml = simplexml_load_string($contents);

                $child = $xml->addChild('verification');
                $child->addChild('merchantId', $parameters->getMerchantId());
                $child->addChild('merchantKey', $parameters->getMerchantKey());
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
