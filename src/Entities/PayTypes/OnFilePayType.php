<?php

namespace Vitorccs\Maxipago\Entities\PayTypes;

use Vitorccs\Maxipago\Entities\Exportable;

class OnFilePayType extends AbstractPayType
{
    use Exportable;

    public function __construct(public int    $customerId,
                                public string $token)
    {

    }

    public function nodeName(): string
    {
        return 'onFile';
    }
}
