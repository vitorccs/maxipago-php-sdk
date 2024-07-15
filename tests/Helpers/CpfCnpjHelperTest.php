<?php

namespace Vitorccs\Maxipago\Test\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Helpers\CpfCnpjHelper;

class CpfCnpjHelperTest extends TestCase
{
    #[DataProvider('maskProvider')]
    public function test_unmask(?string $cpfCnpj,
                                ?string $expected)
    {
        $actual = CpfCnpjHelper::unmask($cpfCnpj);

        $this->assertSame($expected, $actual);
    }

    #[DataProvider('cpfProvider')]
    public function test_is_cpf(?string $cpf,
                                bool    $expected)
    {
        $actual = CpfCnpjHelper::isCpf($cpf);

        $this->assertSame($actual, $expected);
    }

    #[DataProvider('cnpjProvider')]
    public function test_is_cnpj(?string $cnpj,
                                 bool    $expected)
    {
        $actual = CpfCnpjHelper::isCnpj($cnpj);

        $this->assertSame($actual, $expected);
    }

    public static function maskProvider(): array
    {
        return [
            'masked cpf' => [
                '373.067.250-92',
                '37306725092',
            ],
            'masked cnpj' => [
                '50.780.904/0001-55',
                '50780904000155',
            ],
            'unmasked cpf' => [
                '37306725092',
                '37306725092',
            ],
            'unmasked cnpj' => [
                '50780904000155',
                '50780904000155',
            ],
            'null value' => [
                null,
                null
            ]
        ];
    }

    public static function cpfProvider(): array
    {
        return [
            'null' => [
                null,
                false
            ],
            'empty' => [
                '',
                false
            ],
            'numeric invalid length' => [
                '3730672509',
                false
            ],
            'masked cpf' => [
                '373.067.250-92',
                true
            ],
            'unmasked cpf' => [
                '37306725092',
                true
            ]
        ];
    }

    public static function cnpjProvider(): array
    {
        return [
            'null' => [
                null,
                false
            ],
            'empty' => [
                '',
                false
            ],
            'numeric invalid length' => [
                '3730672509',
                false
            ],
            'masked cnpj' => [
                '50.780.904/0001-55',
                true
            ],
            'unmasked cnpj' => [
                '50780904000155',
                true
            ]
        ];
    }
}
