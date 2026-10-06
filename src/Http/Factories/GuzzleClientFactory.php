<?php

namespace Vitorccs\Maxipago\Http\Factories;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use Vitorccs\Maxipago\Entities\Parameters;
use Vitorccs\Maxipago\Http\Middlewares\XmlApiVersionMiddleware;
use Vitorccs\Maxipago\Http\Middlewares\XmlAuthMiddleware;
use Vitorccs\Maxipago\Http\Middlewares\XmlRequestMiddleware;
use Vitorccs\Maxipago\Http\Middlewares\XmlResponseMiddleware;

class GuzzleClientFactory
{
    /**
     * This project version
     */
    const string SDK_VERSION = '1.5.0';

    /**
     * The API Sandbox Base URL
     */
    const string SANDBOX_URL = 'https://testapi.maxipago.net';

    /**
     * The API Production Base URL
     */
    const string PRODUCTION_URL = 'https://api.maxipago.net';

    public static function create(?Parameters $parameters = null): Client
    {
        $parameters ??= new Parameters();

        $handlerStack = new HandlerStack();
        $handlerStack->setHandler(new CurlHandler());
        $handlerStack->push(XmlRequestMiddleware::handle());
        $handlerStack->push(XmlResponseMiddleware::handle());
        $handlerStack->push(XmlAuthMiddleware::handle($parameters));
        $handlerStack->push(XmlApiVersionMiddleware::handle());

        return new Client([
            'base_uri' => static::getApiUrl($parameters),
            'timeout' => $parameters->getTimeout(),
            'handler' => $handlerStack,
            'headers' => [
                'User-Agent' => static::getUserAgent()
            ]
        ]);
    }

    public static function getUserAgent(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';

        return trim("maxipago-php-sdk/" . static::SDK_VERSION . "; {$host}");
    }

    private static function getApiUrl(Parameters $parameters): string
    {
        return $parameters->getSandbox()
            ? self::SANDBOX_URL
            : self::PRODUCTION_URL;
    }
}
