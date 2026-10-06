<?php

namespace Vitorccs\Maxipago\Traits;

use Vitorccs\Maxipago\Constants\Config;

trait TransactionRequestRoot
{
    #[\Override]
    protected function root(): string
    {
        return 'transaction-request';
    }

    #[\Override]
    protected function apiVersion(): ?string
    {
        return Config::API_VERSION;
    }
}
