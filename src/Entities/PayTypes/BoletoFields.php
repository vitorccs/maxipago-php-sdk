<?php

namespace Vitorccs\Maxipago\Entities\PayTypes;

use Vitorccs\Maxipago\Entities\Exportable;

class BoletoFields
{
    use Exportable;

    public string $date;
    public string $type;
    public float $value;
    public ?string $frequency;

    public function __construct(string  $date,
                                string  $type,
                                float   $value,
                                ?string $frequency = null)
    {
        $this->date = $date;
        $this->type = $type;
        $this->value = $value;
        $this->frequency = $frequency;
    }
}
