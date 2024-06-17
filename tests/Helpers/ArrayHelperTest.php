<?php

namespace Vitorccs\Maxipago\Test\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Helpers\ArrayHelper;

class ArrayHelperTest extends TestCase
{
    #[DataProvider('arraysProvider')]
    public function test_remove_empty(array $arrayData, int $levels)
    {
        $newArray = ArrayHelper::removeEmpty($arrayData);

        $this->assertArrayNotHasKey('null', $newArray);
        $this->assertArrayNotHasKey('empty_string', $newArray);
        $this->assertSame($arrayData['zero_integer'],  $newArray['zero_integer']);
        $this->assertSame($arrayData['zero_string'],  $newArray['zero_string']);
        $this->assertSame($arrayData['false_boolean'],  $newArray['false_boolean']);
        $this->assertSame($arrayData['true_boolean'],  $newArray['true_boolean']);
        $this->assertSame($arrayData['empty_array'],  $newArray['empty_array']);

        if ($levels > 1) {
            $this->assertArrayNotHasKey('null', $newArray['level_2']);
            $this->assertArrayNotHasKey('empty_string', $newArray['level_2']);
            $this->assertSame($arrayData['zero_integer'],  $newArray['level_2']['zero_integer']);
            $this->assertSame($arrayData['zero_string'],  $newArray['level_2']['zero_string']);
            $this->assertSame($arrayData['false_boolean'],  $newArray['level_2']['false_boolean']);
            $this->assertSame($arrayData['true_boolean'],  $newArray['level_2']['true_boolean']);
            $this->assertSame($arrayData['empty_array'],  $newArray['level_2']['empty_array']);
        }

        if ($levels > 2) {
            $this->assertArrayNotHasKey('null', $newArray['level_2']['level_3']);
            $this->assertArrayNotHasKey('empty_string', $newArray['level_2']['level_3']);
            $this->assertSame($arrayData['zero_integer'],  $newArray['level_2']['level_3']['zero_integer']);
            $this->assertSame($arrayData['zero_string'],  $newArray['level_2']['level_3']['zero_string']);
            $this->assertSame($arrayData['false_boolean'],  $newArray['level_2']['level_3']['false_boolean']);
            $this->assertSame($arrayData['true_boolean'],  $newArray['level_2']['level_3']['true_boolean']);
            $this->assertSame($arrayData['empty_array'],  $newArray['level_2']['level_3']['empty_array']);
        }
    }

    public static function arraysProvider(): array
    {
        $data = [
            'null' => null,
            'empty_string' => '',
            'zero_integer' => 0,
            'zero_string' => '0',
            'false_boolean' => false,
            'true_boolean' => true,
            'empty_array' => [],
        ];

        return [
            '1 level array' => [
                $data,
                1
            ],
            '2 level array' => [
                array_merge(
                    $data,
                    ['level_2' => $data]
                ),
                2
            ],
            '3 level array' => [
                array_merge(
                    $data,
                    ['level_2' => array_merge(
                        $data,
                        ['level_3' => $data]
                    )]
                ),
                3
            ]
        ];
    }
}
