<?php

namespace Vitorccs\Maxipago\Builders;

use Vitorccs\Maxipago\Entities\SaleSections\BillingData;

class BillingDataBuilder extends AbstractDataBuilder
{
    public function __construct(string $name)
    {
        parent::__construct(new BillingData($name));
    }

    public static function create(string $name): self
    {
        return new self($name);
    }

    public function setCompanyName(?string $name): self
    {
        $this->data->companyName = $name;
        return $this;
    }

    public function get(): BillingData
    {
        return $this->data;
    }
}
