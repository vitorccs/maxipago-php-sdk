<?php

namespace Vitorccs\Maxipago\Http;

use Vitorccs\Maxipago\Entities\Customer;
use Vitorccs\Maxipago\Exceptions\MaxipagoProcessorException;
use Vitorccs\Maxipago\Exceptions\MaxipagoRequestException;
use Vitorccs\Maxipago\Exceptions\MaxipagoValidationException;
use Vitorccs\Maxipago\Traits\ApiRequestRoot;

class CustomerService extends Resource
{
    use ApiRequestRoot;

    /**
     * @throws MaxipagoProcessorException
     * @throws MaxipagoRequestException
     * @throws MaxipagoValidationException
     */
    public function create(Customer|array $customer): ?object
    {
        $customer = $customer instanceof Customer
            ? $customer->export()
            : $customer;

        $data = [
            'request' => $customer
        ];

        return $this->postApi($data, 'add-consumer');
    }
}
