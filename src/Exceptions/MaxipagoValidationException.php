<?php

namespace Vitorccs\Maxipago\Exceptions;

class MaxipagoValidationException extends MaxipagoException
{
    private ?string $errorCode;
    private ?string $responseCode;

    public function __construct(?string $message = null,
                                ?string $errorCode = null,
                                ?string $responseCode = null,
                                int     $httpCode = 0,
                                ?object $responseBody = null)
    {
        parent::__construct($message, $httpCode, $responseBody);

        $this->errorCode = $errorCode;
        $this->responseCode = $responseCode;
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
