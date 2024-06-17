<?php

namespace Vitorccs\Maxipago\Entities\SaleSections;

class PixPayType extends AbstractPayType
{
    public int $expirationTime;
    public ?string $paymentInfo = null;

    public function __construct(int     $expirationTime,
                                ?string $paymentInfo = null)
    {
        $this->expirationTime = $expirationTime;
        $this->paymentInfo = $paymentInfo;
    }

    public function nodeName(): string
    {
        return 'pix';
    }
}
