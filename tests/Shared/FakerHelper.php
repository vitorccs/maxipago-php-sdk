<?php

namespace Vitorccs\Maxipago\Test\Shared;

use Faker\Factory;
use Faker\Generator;

class FakerHelper
{
    /**
     * @var Generator|null
     */
    protected static ?Generator $faker = null;

    /**
     * The Faker default locale
     */
    protected static string $fakerLocale = 'pt_BR';

    /**
     * @return Generator
     */
    public static function get(): Generator
    {
        if (is_null(self::$faker)) {
            self::$faker = Factory::create(self::$fakerLocale);
        }

        return self::$faker;
    }

    public static function randomEnum(string $enumClass)
    {
        $names = array_column($enumClass::cases(), 'name');
        $index = array_rand($names);
        $name = $names[$index];

        return constant($enumClass . "::" . $name);
    }

    public static function randomEnumValue(string $enumClass): mixed
    {
        return self::randomEnum($enumClass)->value;
    }
}
