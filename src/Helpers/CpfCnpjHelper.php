<?php

namespace Vitorccs\Maxipago\Helpers;

class CpfCnpjHelper
{
    /**
     * The CPF chars length
     */
    const CPF_LENGTH = 11;

    /**
     * The CNPJ chars length
     */
    const CNPJ_CHARS_LENGTH = 14;

    public static function unmask(?string $value): ?string
    {
        $trimmed = trim($value ?? '');

        if (!strlen($trimmed)) return null;

        return preg_replace("/[^0-9]/", '', $trimmed);
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
