<?php

namespace Vitorccs\Maxipago\Traits;

trait ApiRequestRoot
{
    protected function root(): string
    {
        return 'api-request';
    }

    protected function apiVersion(): ?string
    {
        return null;
    }
}
