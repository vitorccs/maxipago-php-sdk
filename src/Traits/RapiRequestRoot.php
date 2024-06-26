<?php

namespace Vitorccs\Maxipago\Traits;

use Vitorccs\Maxipago\Constants\Config;

trait RapiRequestRoot
{
    protected function root(): string
    {
        return 'rapi-request';
    }

    protected function apiVersion(): ?string
    {
        return Config::API_VERSION;
    }
}
