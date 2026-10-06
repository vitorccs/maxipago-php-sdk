<?php

namespace Vitorccs\Maxipago\Helpers;

class CpfCnpjHelper
{
    /**
     * The CPF chars length
     */
    const int CPF_LENGTH = 11;

    /**
     * The CNPJ chars length
     */
    const int CNPJ_CHARS_LENGTH = 14;

    /**
     * Removes any non-alphanumeric char and convert to uppercase
     */
    public static function unmask(?string $value): ?string
    {
        $alpha = StringHelper::alphanumericOnly(strtoupper($value ?? ''));

        return strlen($alpha) ? $alpha : null;
    }

    public static function isCpf(?string $value): bool
    {
        $unmasked = self::unmask($value) ?? '';

        return strlen($unmasked) === self::CPF_LENGTH;
    }

    public static function isCnpj(?string $value): bool
    {
        $unmasked = self::unmask($value) ?? '';

        return strlen($unmasked) === self::CNPJ_CHARS_LENGTH;
    }
}
