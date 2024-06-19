<?php

namespace Vitorccs\Maxipago\Test\Entities\Sales\Sections;

use Vitorccs\Maxipago\Entities\Sales\Sections\ShippingData;

class ShippingDataTest extends AbstractDataTest
{
    protected function createDataObject(string $name): ShippingData
    {
        return new ShippingData($name);
    }
}
