<?php

namespace Vitorccs\Maxipago\Entities\PayTypes;

use Vitorccs\Maxipago\Entities\Exportable;

class BoletoFields
{
    use Exportable;

    const DEF_FREQUENCY = 'daily';

    public string $date;
    public string $type;
    public float $value;
    public ?string $frequency;

    public function __construct(string $date,
                                string $type,
                                float  $value,
                                bool   $dailyFrequency = false)
    {
        $this->date = $date;
        $this->type = $type;
        $this->value = $value;
        $this->frequency = $dailyFrequency ? self::DEF_FREQUENCY : null;
    }
}
