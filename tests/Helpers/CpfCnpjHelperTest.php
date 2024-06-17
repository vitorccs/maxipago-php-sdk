<?php

namespace Vitorccs\Maxipago\Test\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Helpers\CpfCnpjHelper;

class CpfCnpjHelperTest extends TestCase
{
    #[DataProvider('cpfCnpjProvider')]
    public function test_unmask(?string $cpfCnpj,
                                ?string $expected)
    {
        $actual = CpfCnpjHelper::unmask($cpfCnpj);

        $this->assertSame($expected, $actual);
    }

    public static function cpfCnpjProvider(): array
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
}
