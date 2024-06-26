<?php

namespace Vitorccs\Maxipago\Helpers;

class NameHelper
{
    public static function getNameParts(string $fullName): array
    {
        $parts = explode(' ', $fullName);

        $firstName = $parts[0];
        $lastName = count($parts) > 1
            ? $parts[count($parts) - 1]
            : null;

        return [$firstName, $lastName];
    }
}
