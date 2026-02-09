<?php

namespace Vitorccs\Maxipago\Exceptions;

class MaxipagoInvalidBodyException extends MaxipagoRequestException
{
    public function __construct(?string $rawBody,
                                int     $httpCode = 0,
                                ?object $responseBody = null)
    {
        $contents = !empty($rawBody) ? $rawBody : 'body is empty';
        $message = sprintf("Malformed XML response from Maxipago (%s)", $contents);

        parent::__construct($message, $httpCode, $responseBody);
    }
}
