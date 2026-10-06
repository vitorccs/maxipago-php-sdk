<?php

namespace Vitorccs\Maxipago\Test\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Helpers\StringHelper;

class StringHelperTest extends TestCase
{
    #[DataProvider('alphanumericProvider')]
    public function test_alphanumeric_only(string $value,
                                           string $expected)
    {
        $actual = StringHelper::alphanumericOnly($value);

        $this->assertSame($expected, $actual);
    }

    public static function alphanumericProvider(): array
    {
        return [
            'empty' => [
                '',
                ''
            ],
            'numbers only' => [
                '0123456789',
                '0123456789'
            ],
            'uppercase letters' => [
                'ABCXYZ',
                'ABCXYZ'
            ],
            'lowercase letters' => [
                'abcxyz',
                'abcxyz'
            ],
            'mixed case is preserved' => [
                'aBc123XyZ',
                'aBc123XyZ'
            ],
            'masked cpf' => [
                '373.067.250-92',
                '37306725092'
            ],
            'masked alphanumeric cnpj' => [
                '12.ABC.345/01DE-35',
                '12ABC34501DE35'
            ],
            'whitespace' => [
                " a b\tc\n1 ",
                'abc1'
            ],
            'special chars only' => [
                '!@#$%^&*()_+-=[]{};:\'",.<>/?\\|`~',
                ''
            ],
            'accented letters are removed' => [
                'ação123',
                'ao123'
            ],
        ];
    }
}
