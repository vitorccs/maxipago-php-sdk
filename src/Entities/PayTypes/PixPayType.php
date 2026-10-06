<?php

namespace Vitorccs\Maxipago\Entities\PayTypes;

class PixPayType extends AbstractPayType
{
    public function __construct(public int     $expirationTime,
                                public ?string $paymentInfo = null)
    {
    }

    #[\Override]
    public function nodeName(): string
    {
        return 'pix';
    }
}
