<?php

namespace Vitorccs\Maxipago\Entities\Sales\Sections;

use JsonSerializable;
use Vitorccs\Maxipago\Entities\Exportable;

class Payment implements JsonSerializable
{
    use Exportable;

    public float $chargeTotal;
    public ?float $shippingTotal = null;
    public ?string $currencyCode = null;
    public ?string $softDescriptor = null;

    public function __construct(float $chargeTotal)
    {
        $this->chargeTotal = $chargeTotal;
    }
}
