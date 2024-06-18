<?php

namespace Vitorccs\Maxipago\Builders;

use Vitorccs\Maxipago\Entities\Sales\Sections\ShippingData;

class ShippingDataBuilder extends AbstractDataBuilder
{
    public function __construct(string $name)
    {
        parent::__construct(new ShippingData($name));
    }

    public static function create(string $name): self
    {
        return new self($name);
    }

    public function get(): ShippingData
    {
        return $this->data;
    }
}
