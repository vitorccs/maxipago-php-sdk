<?php

namespace Vitorccs\Maxipago\Http;

use Vitorccs\Maxipago\Entities\CreditCard;
use Vitorccs\Maxipago\Entities\Customer;
use Vitorccs\Maxipago\Exceptions\MaxipagoRequestException;
use Vitorccs\Maxipago\Exceptions\MaxipagoValidationException;
use Vitorccs\Maxipago\Traits\ApiRequestRoot;

class CustomerService extends Resource
{
    use ApiRequestRoot;

    /**
     * @throws MaxipagoRequestException
     * @throws MaxipagoValidationException
     */
    public function create(Customer|array $customer): int
    {
        $customer = $customer instanceof Customer
            ? $customer->export()
            : $customer;

        $data = [
            'request' => $customer
        ];

        return $this->postApi($data, 'add-consumer')->result->customerId;
    }

    /**
     * @throws MaxipagoRequestException
     * @throws MaxipagoValidationException
     */
    public function saveCard(CreditCard|array $customerCard): string
    {
        $customer = $customerCard instanceof CreditCard
            ? $customerCard->export()
            : $customerCard;

        $data = [
            'request' => $customer
        ];

        return $this->postApi($data, 'add-card-onfile')->result->token;
    }
}
