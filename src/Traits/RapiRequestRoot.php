<?php

namespace Vitorccs\Maxipago\Traits;

use Vitorccs\Maxipago\Constants\Config;

trait RapiRequestRoot
{
    #[\Override]
    protected function root(): string
    {
        return 'rapi-request';
    }

    #[\Override]
    protected function apiVersion(): ?string
    {
        return Config::API_VERSION;
    }
}
