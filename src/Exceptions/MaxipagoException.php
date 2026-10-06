<?php

namespace Vitorccs\Maxipago\Exceptions;

class MaxipagoException extends \Exception
{
    public function __construct(?string                  $message = null,
                                int                      $httpCode = 0,
                                private readonly ?object $responseBody = null)
    {
        $message = trim($message ?: 'Undefined error');
        parent::__construct($message, $httpCode);
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
