<?php

namespace Vitorccs\Maxipago\Test\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Exceptions\MaxipagoException;
use Vitorccs\Maxipago\Helpers\DateHelper;

class DateHelperTest extends TestCase
{
    #[DataProvider('validStdDatesProvider')]
    public function test_valid_std_dates(\DateTime|string|null $date,
                                         ?string               $expected)
    {
        $actual = DateHelper::toString($date);

        $this->assertSame($actual, $expected);
    }

    #[DataProvider('validLocalDatesProvider')]
    public function test_valid_local_dates(\DateTime|string|null $date,
                                           ?string               $expected)
    {
        $actual = DateHelper::toLocalString($date);

        $this->assertSame($actual, $expected);
    }

    #[DataProvider('invalidStdDatesProvider')]
    public function test_invalid_std_dates(\DateTime|string|null $date)
    {
        $this->expectException(MaxipagoException::class);

        DateHelper::toString($date);
    }

    #[DataProvider('invalidLocalDatesProvider')]
    public function test_invalid_local_dates(\DateTime|string|null $date)
    {
        $this->expectException(MaxipagoException::class);

        DateHelper::toLocalString($date);
    }

    public static function validStdDatesProvider(): array
    {
        return [
            'empty' => [
                '',
                null
            ],
            'null' => [
                null,
                null
            ],
            'string' => [
                '2024-12-25',
                '2024-12-25',
            ],
            'Datetime' => [
                new \DateTime('2024-12-25'),
                '2024-12-25',
            ],
        ];
    }

    public static function validLocalDatesProvider(): array
    {
        return [
            'empty non intl' => [
                '',
                null
            ],
            'null non intl' => [
                null,
                null
            ],
            'string non intl' => [
                '25/12/2024',
                '25/12/2024',
            ],
            'Datetime non intl' => [
                new \DateTime('2024-12-25'),
                '25/12/2024',
            ]
        ];
    }

    public static function invalidStdDatesProvider(): array
    {
        return [
            'wrong separators' => [
                '25-12-2024',
            ],
            'wrong format' => [
                '25/12/204',
            ],
            'numeric' => [
                '1718811873',
            ]
        ];
    }

    public static function invalidLocalDatesProvider(): array
    {
        return [
            'wrong separators' => [
                '25-12-2024',
            ],
            'wrong format' => [
                '2024-12-25',
            ],
            'numeric' => [
                '1718811873',
            ]
        ];
    }
}
