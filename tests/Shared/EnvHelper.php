<?php

namespace Vitorccs\Maxipago\Test\Shared;

use Vitorccs\Maxipago\Entities\Parameters;

class EnvHelper
{
    /**
     * Reset env parameters to empty
     */
    public static function resetEnv(): void
    {
        $keys = [
            Parameters::MAXIPAGO_MERCHANT_ID,
            Parameters::MAXIPAGO_MERCHANT_KEY,
            Parameters::MAXIPAGO_SANDBOX,
            Parameters::MAXIPAGO_TIMEOUT,
        ];

        foreach ($keys as $key) {
            putenv($key);
        }
    }

    /**
     * Set env parameters
     */
    public static function setEnv(array $keyValues): array
    {
        foreach ($keyValues as $key => $value) {
            if (is_bool($value)) {
                $value = json_encode($value);
            }

            is_null($value)
                ? putenv($key)
                : putenv("$key=$value");
        }

        return $keyValues;
    }

    /**
     * Set env parameters with fake values
     */
    public static function setFakeEnv(?string $merchantId = null,
                                      ?string $merchantKey = null,
                                      ?bool   $sandbox = null,
                                      ?int    $timeout = null): array
    {
        $faker = FakerHelper::get();

        $keyValues = [
            Parameters::MAXIPAGO_MERCHANT_ID => $merchantId ?: $faker->uuid(),
            Parameters::MAXIPAGO_MERCHANT_KEY => $merchantKey ?: $faker->uuid(),
            Parameters::MAXIPAGO_SANDBOX => $sandbox ?: $faker->boolean(),
            Parameters::MAXIPAGO_TIMEOUT => $timeout ?: $faker->numberBetween(1)
        ];

        return static::setEnv($keyValues);
    }
}
