<?php

namespace Vitorccs\Maxipago\Helpers;

class StringHelper
{
    public static function alphanumericOnly(string $value): string
    {
        return preg_replace("/[^0-9A-Z]/i", '', $value);
    }
}
