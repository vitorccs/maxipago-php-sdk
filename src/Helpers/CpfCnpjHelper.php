<?php

namespace Vitorccs\Maxipago\Helpers;

class CpfCnpjHelper
{
    public static function unmask(?string $value): ?string
    {
        $trimmed = trim($value ?? '');

        if (!strlen($trimmed)) return null;

        return preg_replace("/[^0-9]/", '', $trimmed);
    }
}
