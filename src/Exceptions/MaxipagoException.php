<?php

namespace Vitorccs\Maxipago\Exceptions;

class MaxipagoException extends \Exception
{
    private ?object $responseBody;

    public function __construct(string  $message = null,
                                int     $httpCode = 0,
                                ?object $responseBody = null)
    {
        $message = trim($message ?: 'Undefined error');
        parent::__construct($message, $httpCode);

        $this->responseBody = $responseBody;
    }

    public function getResponseBody(): ?object
    {
        return $this->responseBody;
    }

    public function getHttpCode(): int
    {
        return $this->getCode();
    }
}
