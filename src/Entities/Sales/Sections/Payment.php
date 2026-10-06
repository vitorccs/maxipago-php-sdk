<?php

namespace Vitorccs\Maxipago\Entities\Sales\Sections;

use JsonSerializable;
use Vitorccs\Maxipago\Entities\Exportable;

class Payment implements JsonSerializable
{
    use Exportable;

    public function __construct(public float $chargeTotal)
    {
    }

    // declared after the constructor to preserve the XML node order (see Exportable)
    public ?float $shippingTotal = null;
    public ?string $currencyCode = null;
    public ?string $softDescriptor = null;
}
