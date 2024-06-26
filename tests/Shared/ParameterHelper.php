<?php

namespace Vitorccs\Maxipago\Test\Shared;

use Vitorccs\Maxipago\Entities\Parameters;

class ParameterHelper
{
    public static function create(): Parameters
    {
        $faker = FakerHelper::get();

        return new Parameters($faker->uuid(), $faker->uuid());
    }
}
