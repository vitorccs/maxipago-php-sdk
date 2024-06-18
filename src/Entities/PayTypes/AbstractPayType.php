<?php

namespace Vitorccs\Maxipago\Entities\PayTypes;

use JsonSerializable;
use Vitorccs\Maxipago\Entities\Exportable;

abstract class AbstractPayType implements JsonSerializable
{
    use Exportable;

    abstract public function nodeName(): string;
}
