<?php

namespace Vitorccs\Maxipago\Test\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Helpers\CreditCardHelper;

class CreditCardHelperTest extends TestCase
{
    #[DataProvider('monthProvider')]
    public function test_normalize_month(string|int $month,
                                         string     $expected)
    {
        $actual = CreditCardHelper::normalizeMonth($month);

        $this->assertEquals($expected, $actual);
    }

    #[DataProvider('numberProvider')]
    public function test_unmask_number(string $number,
                                       string $expected)
    {
        $actual = CreditCardHelper::unmaskNumber($number);

        $this->assertEquals($expected, $actual);
    }

    public static function monthProvider(): array
    {
        return [
            'integer 1' => [
                1,
                '01'
            ],
            'string 1' => [
                '1',
                '01'
            ],
            'string 01' => [
                '01',
                '01'
            ],
            'integer 9' => [
                9,
                '09'
            ],
            'string 9' => [
                '9',
                '09'
            ],
            'string 09' => [
                '09',
                '09'
            ],
            'integer 10' => [
                10,
                '10'
            ],
            'string 10' => [
                '10',
                '10'
            ],
        ];
    }

    public static function numberProvider(): array
    {
        return [
            'spaces' => [
                '5555 5555 5555 55557',
                '55555555555555557'
            ],
            'other chars' => [
                '5555.5555_5555-55557',
                '55555555555555557'
            ],
        ];
    }
}
