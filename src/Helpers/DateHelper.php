<?php

namespace Vitorccs\Maxipago\Helpers;

use Vitorccs\Maxipago\Exceptions\MaxipagoException;

class DateHelper
{
    /**
     * @throws MaxipagoException
     */
    public static function toLocalString(\DateTime|string|null $date): ?string
    {
        return static::formatDate($date, 'd/m/Y');
    }

    /**
     * @throws MaxipagoException
     */
    public static function toString(\DateTime|string|null $date): ?string
    {
        return static::formatDate($date, 'Y-m-d');
    }

    /**
     * @throws MaxipagoException
     */
    private static function formatDate(\DateTime|string|null $date,
                                       string                $format): ?string
    {
        if (empty($date)) return null;

        if (is_string($date)) {
            static::validateStringDate($date, $format);
        }

        return $date instanceof \DateTime
            ? $date->format($format)
            : $date;
    }

    /**
     * @throws MaxipagoException
     */
    private static function validateStringDate(string $date,
                                               string $format): void
    {
        $regExp = '#^' . strtr($format, [
            'd' => '\d{1,2}',
            'm' => '\d{1,2}',
            'Y' => '\d{4}',
        ]) . '$#';

        if (!preg_match($regExp, $date)) {
            throw new MaxipagoException('Invalid date format');
        }
    }
}
