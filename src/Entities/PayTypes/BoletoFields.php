<?php

namespace Vitorccs\Maxipago\Entities\PayTypes;

use Vitorccs\Maxipago\Entities\Exportable;

class BoletoFields
{
    use Exportable;

    const string DEF_FREQUENCY = 'daily';

    public function __construct(public string $date,
                                public string $type,
                                public float  $value,
                                bool          $dailyFrequency = false)
    {
        $this->frequency = $dailyFrequency ? self::DEF_FREQUENCY : null;
    }

    // declared after the constructor to preserve the XML node order (see Exportable)
    public ?string $frequency;
}
