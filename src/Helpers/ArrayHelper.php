<?php

namespace Vitorccs\Maxipago\Helpers;

class ArrayHelper
{
    /**
     * Remove array entries with empty string or null values
     */
    public static function removeEmpty(array $haystack): array
    {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $haystack[$key] = self::removeEmpty($value);
            }

            if (in_array($value, ['', null], true)) {
                unset($haystack[$key]);
            }
        }

        return $haystack;
    }
}
