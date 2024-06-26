<?php

namespace Vitorccs\Maxipago\Exceptions;

class MaxipagoProcessorException extends MaxipagoException
{
    private ?string $processorCode;
    private ?string $responseCode;

    public function __construct(?string $message = null,
                                ?string $processorCode = null,
                                ?string $responseCode = null,
                                int     $httpCode = 0,
                                ?object $responseBody = null)
    {
        parent::__construct($message, $httpCode, $responseBody);

        $this->responseCode = $responseCode;
        $this->processorCode = $processorCode;
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
