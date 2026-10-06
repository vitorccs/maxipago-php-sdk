<?php

namespace Vitorccs\Maxipago\Exceptions;

class MaxipagoProcessorException extends MaxipagoException
{
    public function __construct(?string                  $message = null,
                                private readonly ?string $processorCode = null,
                                private readonly ?string $responseCode = null,
                                int                      $httpCode = 0,
                                ?object                  $responseBody = null)
    {
        parent::__construct($message, $httpCode, $responseBody);
    }

    public function getResponseCode(): ?string
    {
        return $this->responseCode;
    }

    public function getProcessorCode(): ?string
    {
        return $this->processorCode;
    }
}
