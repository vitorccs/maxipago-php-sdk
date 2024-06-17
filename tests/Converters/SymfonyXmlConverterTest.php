<?php

namespace Vitorccs\Maxipago\Test\Converters;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vitorccs\Maxipago\Converters\SymfonyXmlConverter;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class SymfonyXmlConverterTest extends TestCase
{
    #[DataProvider('decodeArrayProvider')]
    public function test_decode_array(string $xmlContent,
                                      array  $expPayload)
    {
        $converter = new SymfonyXmlConverter();
        $payload = $converter->decodeArray($xmlContent);

        $this->assertSame($payload, $expPayload);
    }

    #[DataProvider('decodeObjectProvider')]
    public function test_decode_object(string $xmlContent,
                                       object $expPayload)
    {
        $converter = new SymfonyXmlConverter();
        $payload = $converter->decodeObject($xmlContent);

        $this->assertSame(serialize($payload), serialize($expPayload));
    }

    #[DataProvider('encodeProvider')]
    public function test_from_array(array  $payload,
                                    string $expXmlContent,
                                    string $root)
    {
        $converter = new SymfonyXmlConverter();
        $xmlContent = $converter->encode($payload, $root);

        $this->assertStringContainsString($expXmlContent, $xmlContent);
    }

    public static function decodeArrayProvider(): array
    {
        $faker = FakerHelper::get();

        $root = $faker->word();
        $integer = $faker->numberBetween();
        $word = $faker->word();
        $sentence = $faker->paragraph(1);

        $xmlContent = "<{$root}>" .
            '<empty></empty>' .
            '<inline-empty/>' .
            "<integer>{$integer}</integer>" .
            "<word>{$word}</word>" .
            "<sentence>{$sentence}</sentence>" .
            "<sentence-cdata><![CDATA[" . $sentence . "]]></sentence-cdata>" .
            "<sentence-cdata-space>\n\t<![CDATA[" . $sentence . "]]>\n\t</sentence-cdata-space>" .
            '<children>' .
            '<child-1>a</child-1>' .
            '<child-2>b</child-2>' .
            '<child-3>c</child-3>' .
            '<child-4></child-4>' .
            '<child-5></child-5>' .
            '<inner-children>' .
            '<child-1>a</child-1>' .
            '<child-2>b</child-2>' .
            '<child-3>c</child-3>' .
            '<child-4></child-4>' .
            '<child-5></child-5>' .
            '</inner-children>' .
            '</children>' .
            '<collection>' .
            '<items>' .
            '<item>1</item>' .
            '<item>2</item>' .
            '<item>3</item>' .
            '</items>' .
            '</collection>' .
            "</{$root}>";

        $expPayload = [
            'empty' => '',
            'inline-empty' => '',
            'integer' => (string)$integer,
            'word' => $word,
            'sentence' => $sentence,
            'sentence-cdata' => $sentence,
            'sentence-cdata-space' => $sentence,
            'children' => [
                'child-1' => 'a',
                'child-2' => 'b',
                'child-3' => 'c',
                'child-4' => '',
                'child-5' => '',
                'inner-children' => [
                    'child-1' => 'a',
                    'child-2' => 'b',
                    'child-3' => 'c',
                    'child-4' => '',
                    'child-5' => '',
                ]
            ],
            'collection' => [
                'items' => [
                    'item' => [
                        '0' => '1',
                        '1' => '2',
                        '2' => '3'
                    ]
                ]
            ]
        ];

        return [
            'without xml declaration' => [
                $xmlContent,
                $expPayload
            ],
            'with xml declaration' => [
                '<?xml version="1.0" encoding="UTF-8" ?>' . $xmlContent,
                $expPayload
            ]
        ];
    }

    public static function decodeObjectProvider(): array
    {
        $faker = FakerHelper::get();

        $root = $faker->word();
        $integer = $faker->numberBetween();
        $word = $faker->word();
        $sentence = $faker->paragraph(1);

        $xmlContent = "<{$root}>" .
            '<empty></empty>' .
            '<inline-empty/>' .
            "<integer>{$integer}</integer>" .
            "<word>{$word}</word>" .
            "<sentence>{$sentence}</sentence>" .
            "<sentence-cdata><![CDATA[" . $sentence . "]]></sentence-cdata>" .
            "<sentence-cdata-space>\n\t<![CDATA[" . $sentence . "]]>\n\t</sentence-cdata-space>" .
            '<children>' .
            '<child-1>a</child-1>' .
            '<child-2>b</child-2>' .
            '<child-3>c</child-3>' .
            '<child-4></child-4>' .
            '<child-5></child-5>' .
            '<inner-children>' .
            '<child-1>a</child-1>' .
            '<child-2>b</child-2>' .
            '<child-3>c</child-3>' .
            '<child-4></child-4>' .
            '<child-5></child-5>' .
            '</inner-children>' .
            '</children>' .
            '<collection>' .
            '<items>' .
            '<item>1</item>' .
            '<item>2</item>' .
            '<item>3</item>' .
            '</items>' .
            '</collection>' .
            "</{$root}>";

        $expPayload = (object)[
            'empty' => '',
            'inline-empty' => '',
            'integer' => (string)$integer,
            'word' => $word,
            'sentence' => $sentence,
            'sentence-cdata' => $sentence,
            'sentence-cdata-space' => $sentence,
            'children' => (object)[
                'child-1' => 'a',
                'child-2' => 'b',
                'child-3' => 'c',
                'child-4' => '',
                'child-5' => '',
                'inner-children' => (object)[
                    'child-1' => 'a',
                    'child-2' => 'b',
                    'child-3' => 'c',
                    'child-4' => '',
                    'child-5' => '',
                ]
            ],
            'collection' => (object)[
                'items' => (object)[
                    'item' => [
                        '0' => '1',
                        '1' => '2',
                        '2' => '3'
                    ]
                ]
            ]
        ];

        return [
            'without xml declaration' => [
                $xmlContent,
                $expPayload
            ],
            'with xml declaration' => [
                '<?xml version="1.0" encoding="UTF-8" ?>' . $xmlContent,
                $expPayload
            ]
        ];
    }

    public static function encodeProvider(): array
    {
        $faker = FakerHelper::get();

        $root = $faker->word();
        $integer = $faker->numberBetween();
        $word = $faker->word();
        $sentence = $faker->paragraph(1);

        $payload = [
            'empty' => null,
            'inline-empty' => null,
            'integer' => $integer,
            'word' => $word,
            'sentence' => $sentence,
            'sentence-cdata' => $sentence,
            'sentence-cdata-space' => $sentence,
            'special-chars' => [
                'char' => [
                    'ç',
                    'Ç',
                    'áàâã',
                    'ÁÀÂÃ',
                    'éèêẽ',
                    'ÉÈÊẼ',
                    'íìîĩ',
                    'ÍÌÎĨ',
                    'óòôõ',
                    'ÓÒÔÕ',
                    'úùûũ',
                    'ÚÙÛŨ',
                    '&',
                    '<>',
                    '"'
                ],
            ],
            'children' => [
                'child-1' => 'a',
                'child-2' => 'b',
                'child-3' => 'c',
                'child-4' => null,
                'child-5' => '',
                'inner-children' => [
                    'child-1' => 'a',
                    'child-2' => 'b',
                    'child-3' => 'c',
                    'child-4' => null,
                    'child-5' => '',
                ]
            ],
            'collection' => [
                'items' => [
                    'item' => [
                        '0' => 1,
                        '1' => 2,
                        '2' => 3
                    ]
                ]
            ]
        ];

        $expXmlContent = "<{$root}>" .
            "<integer>{$integer}</integer>" .
            "<word>{$word}</word>" .
            "<sentence>{$sentence}</sentence>" .
            "<sentence-cdata>{$sentence}</sentence-cdata>" .
            "<sentence-cdata-space>{$sentence}</sentence-cdata-space>" .
            "<special-chars>" .
            "<char><![CDATA[ç]]></char>" .
            "<char><![CDATA[Ç]]></char>" .
            "<char><![CDATA[áàâã]]></char>" .
            "<char><![CDATA[ÁÀÂÃ]]></char>" .
            "<char><![CDATA[éèêẽ]]></char>" .
            "<char><![CDATA[ÉÈÊẼ]]></char>" .
            "<char><![CDATA[íìîĩ]]></char>" .
            "<char><![CDATA[ÍÌÎĨ]]></char>" .
            "<char><![CDATA[óòôõ]]></char>" .
            "<char><![CDATA[ÓÒÔÕ]]></char>" .
            "<char><![CDATA[úùûũ]]></char>" .
            "<char><![CDATA[ÚÙÛŨ]]></char>" .
            "<char><![CDATA[&]]></char>" .
            "<char><![CDATA[<>]]></char>" .
            '<char><![CDATA["]]></char>' .
            "</special-chars>" .
            '<children>' .
            '<child-1>a</child-1>' .
            '<child-2>b</child-2>' .
            '<child-3>c</child-3>' .
            '<inner-children>' .
            '<child-1>a</child-1>' .
            '<child-2>b</child-2>' .
            '<child-3>c</child-3>' .
            '</inner-children>' .
            '</children>' .
            '<collection>' .
            '<items>' .
            '<item>1</item>' .
            '<item>2</item>' .
            '<item>3</item>' .
            '</items>' .
            '</collection>' .
            "</{$root}>";

        return [
            'valid_data' => [
                $payload,
                $expXmlContent,
                $root
            ]
        ];
    }
}
