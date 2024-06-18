<?php

namespace Vitorccs\Maxipago\Test\Entities\SaleSections;

use PHPUnit\Framework\Attributes\DataProvider;
use Vitorccs\Maxipago\Entities\Sales\Sections\BillingData;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class BillingDataTest extends AbstractDataTest
{
    protected function createDataObject(string $name): BillingData
    {
        return new BillingData($name);
    }

    #[DataProvider('billingDataProvider')]
    public function test_company_name_export(string  $name,
                                             ?string $companyName)
    {
        $obj = $this->createDataObject($name);
        $obj->companyName = $companyName;
        $export = $obj->export();

        $this->assertSame($companyName, $export['companyName'] ?? null);
    }

    public static function billingDataProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'null values' => [
                $faker->name(),
                null
            ],
            'non-null values' => [
                $faker->name(),
                $faker->company()
            ]
        ];
    }
}
