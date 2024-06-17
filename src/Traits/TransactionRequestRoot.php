<?php

namespace Vitorccs\Maxipago\Traits;

use Vitorccs\Maxipago\Constants\Config;

trait TransactionRequestRoot
{
    protected function root(): string
    {
        return 'transaction-request';
    }

    protected function apiVersion(): ?string
    {
        return Config::API_VERSION;
    }
}
