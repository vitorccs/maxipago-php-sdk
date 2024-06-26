<?php

namespace Vitorccs\Maxipago\Test\Builders;

use PHPUnit\Framework\Attributes\DataProvider;
use Vitorccs\Maxipago\Builders\BillingDataBuilder;
use Vitorccs\Maxipago\Entities\Sales\Sections\BillingData;
use Vitorccs\Maxipago\Test\Shared\FakerHelper;

class BillingDataBuilderTest extends AbstractDataBuilderTester
{
    public function getBuilder(string $name): BillingDataBuilder
    {
        return new BillingDataBuilder($name);
    }

    public function instance(): string
    {
        return BillingData::class;
    }

    #[DataProvider('billingFieldsProvider')]
    public function test_billing_fields(string  $name,
                                        ?string $companyName)
    {
        $builder = $this->getBuilder($name);
        $builder->setCompanyName($companyName);
        $data = $builder->get();

        $this->assertInstanceOf($this->instance(), $data);
        $this->assertSame($name, $data->name);
        $this->assertSame($companyName, $data->companyName);
    }

    public static function billingFieldsProvider(): array
    {
        $faker = FakerHelper::get();

        return [
            'null values' => [
                $faker->word(),
                null
            ],
            'non-null values' => [
                $faker->word(),
                $faker->company()
            ],
        ];
    }
}
