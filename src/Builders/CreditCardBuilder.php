<?php

namespace Vitorccs\Maxipago\Builders;

use Vitorccs\Maxipago\Entities\CreditCard;
use Vitorccs\Maxipago\Helpers\CreditCardHelper;

class CreditCardBuilder
{
    private CreditCard $customer;

    public function __construct(int        $customerId,
                                string     $creditCardNumber,
                                string|int $expirationMonth,
                                int        $expirationYear)
    {
        $expirationMonth = CreditCardHelper::normalizeMonth($expirationMonth);
        $creditCardNumber = CreditCardHelper::unmaskNumber($creditCardNumber);

        $this->customer = new CreditCard(
            $customerId,
            $creditCardNumber,
            $expirationMonth,
            $expirationYear
        );
    }

    public static function create(int    $customerId,
                                  string $creditCardNumber,
                                  int    $expirationMonth,
                                  int    $expirationYear): self
    {
        return new self(
            $customerId,
            $creditCardNumber,
            $expirationMonth,
            $expirationYear
        );
    }

    public function get(): CreditCard
    {
        return $this->customer;
    }

    public function setBillingName(?string $billingName): self
    {
        $this->customer->billingName = $billingName;
        return $this;
    }

    public function setBillingPhone(?string $billingPhone): self
    {
        $this->customer->billingPhone = $billingPhone;
        return $this;
    }

    public function setBillingEmail(?string $billingEmail): self
    {
        $this->customer->billingEmail = $billingEmail;
        return $this;
    }

    public function setBillingAddressFields(string  $address,
                                            ?string $address2,
                                            string  $city,
                                            string  $state,
                                            string  $postalCode,
                                            ?string $country = null): self
    {
        $this->customer->billingAddress1 = $address;
        $this->customer->billingAddress2 = $address2;
        $this->customer->billingCity = $city;
        $this->customer->billingState = $state;
        $this->customer->billingZip = $postalCode;
        $this->customer->billingCountry = $country;

        return $this;
    }
}
