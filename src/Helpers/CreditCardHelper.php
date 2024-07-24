<?php

namespace Vitorccs\Maxipago\Helpers;

class CreditCardHelper
{
    public static function normalizeMonth(string|int $month): string
    {
        return strlen($month) < 2 ? "0{$month}" : $month;
    }

    public static function unmaskNumber(string $number): string
    {
        return preg_replace('/[^0-9]/', '', $number);
    }
}
