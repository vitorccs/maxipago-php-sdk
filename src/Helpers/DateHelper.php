<?php

namespace Vitorccs\Maxipago\Helpers;

class DateHelper
{
    public static function toString(\DateTime|string|null $date,
                                    string                $format = 'Y-m-d'): ?string
    {
        if (empty($date)) return null;

        return $date instanceof \DateTime
            ? $date->format($format)
            : $date;
    }
}
