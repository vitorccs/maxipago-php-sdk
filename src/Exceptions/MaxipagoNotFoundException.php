<?php

namespace Vitorccs\Maxipago\Exceptions;

class MaxipagoNotFoundException extends MaxipagoException
{
    public function __construct(?object $responseBody = null)
    {
        parent::__construct('Transaction not found', 200, $responseBody);
    }
}
