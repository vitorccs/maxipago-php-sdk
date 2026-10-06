<?php

namespace Vitorccs\Maxipago\Traits;

trait ApiRequestRoot
{
    #[\Override]
    protected function root(): string
    {
        return 'api-request';
    }

    #[\Override]
    protected function apiVersion(): ?string
    {
        return null;
    }
}
