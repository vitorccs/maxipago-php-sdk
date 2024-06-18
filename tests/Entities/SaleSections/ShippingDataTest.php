<?php

namespace Vitorccs\Maxipago\Test\Entities\SaleSections;

use Vitorccs\Maxipago\Entities\Sales\Sections\ShippingData;

class ShippingDataTest extends AbstractDataTest
{
    protected function createDataObject(string $name): ShippingData
    {
        return new ShippingData($name);
    }
}
