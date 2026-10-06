<?php

namespace Vitorccs\Maxipago\Exceptions;

class MaxipagoValidationException extends MaxipagoException
{
    public function __construct(?string                  $message = null,
                                private readonly ?string $errorCode = null,
                                private readonly ?string $responseCode = null,
                                int                      $httpCode = 0,
                                ?object                  $responseBody = null)
    {
        parent::__construct($message, $httpCode, $responseBody);
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getResponseCode(): ?string
    {
        return $this->responseCode;
    }
}
