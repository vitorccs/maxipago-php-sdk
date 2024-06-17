<?php

namespace Vitorccs\Maxipago\Test\Builders;

use Vitorccs\Maxipago\Builders\ShippingDataBuilder;
use Vitorccs\Maxipago\Entities\SaleSections\ShippingData;

class ShippingDataBuilderTest extends AbstractDataBuilderTester
{
    public function getBuilder(string $name): ShippingDataBuilder
    {
        return new ShippingDataBuilder($name);
    }

    public function instance(): string
    {
        return ShippingData::class;
    }
}
