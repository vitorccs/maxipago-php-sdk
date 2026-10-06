<?php

namespace Vitorccs\Maxipago\Entities\PayTypes;


class OnFilePayType extends AbstractPayType
{
    public function __construct(public int    $customerId,
                                public string $token)
    {
    }

    #[\Override]
    public function nodeName(): string
    {
        return 'onFile';
    }
}
