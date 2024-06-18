<?php

namespace Vitorccs\Maxipago\Builders;

use Vitorccs\Maxipago\Entities\PayTypes\AbstractPayType;
use Vitorccs\Maxipago\Entities\Sales\AbstractSale;
use Vitorccs\Maxipago\Entities\Sales\Sections\Address;
use Vitorccs\Maxipago\Entities\Sales\Sections\BillingData;
use Vitorccs\Maxipago\Entities\Sales\Sections\ShippingData;
use Vitorccs\Maxipago\Enums\Answer;
use Vitorccs\Maxipago\Enums\CustomerGender;
use Vitorccs\Maxipago\Enums\CustomerType;

abstract class AbstractSaleBuilder
{
    protected AbstractSale $sale;
    protected AbstractPayType $payType;

    public function __construct(AbstractSale    $sale,
                                AbstractPayType $payType)
    {
        $this->sale = $sale;
        $this->payType = $payType;
    }

    public function get(): AbstractSale
    {
        return $this->sale;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->sale->ipAddress = $ipAddress;
        return $this;
    }

    public function setFraudCheck(Answer $answer): self
    {
        $this->sale->fraudCheck = $answer->value;
        return $this;
    }

    public function setCustomerIdCheck(?string $customerIdExt): self
    {
        $this->sale->customerIdExt = $customerIdExt;
        return $this;
    }

    public function setBilling(BillingDataBuilder|BillingData|null $billingData): self
    {
        $billing = $billingData instanceof BillingDataBuilder
            ? $billingData->get()
            : $billingData;

        $this->sale->billing = $billing;
        return $this;
    }

    public function setShipping(?ShippingData $shippingData): self
    {
        $this->sale->shipping = $shippingData;
        return $this;
    }

    public function setPaymentCurrencyCode(?string $currencyCode): self
    {
        $this->sale->payment->currencyCode = $currencyCode;
        return $this;
    }

    public function setPaymentShippingTotal(?float $shippingTotal): self
    {
        $this->sale->payment->shippingTotal = $shippingTotal;
        return $this;
    }

    public function setPaymentSoftDescriptor(?string $softDescriptor): self
    {
        $this->sale->payment->softDescriptor = $softDescriptor;
        return $this;
    }

    public function createBilling(string          $name,
                                  ?string         $cpf = null,
                                  ?string         $rg = null,
                                  ?string         $email = null,
                                  ?string         $phone = null,
                                  ?string         $companyName = null,
                                  ?\DateTime      $birthdate = null,
                                  ?Address        $address = null,
                                  ?CustomerGender $gender = null,
                                  ?CustomerType   $customerType = null): self
    {
        $billing = BillingDataBuilder::create($name)
            ->setCpf($cpf)
            ->setPhone($phone)
            ->setEmail($email)
            ->setRg($rg)
            ->setAddress($address)
            ->setBirthdate($birthdate)
            ->setCompanyName($companyName)
            ->setCustomerType($customerType)
            ->setGender($gender)
            ->get();

        $this->sale->billing = $billing;

        return $this;
    }

    public function setBillingAddressFields(string  $address,
                                            ?string $address2,
                                            string  $district,
                                            string  $city,
                                            string  $state,
                                            string  $postalcode,
                                            ?string $country = null): self
    {
        $this->sale->billing->setAddressFields(
            $address,
            $address2,
            $district,
            $city,
            $state,
            $postalcode,
            $country
        );
        return $this;
    }

    public function createShipping(string          $name,
                                   ?string         $cpf = null,
                                   ?string         $rg = null,
                                   ?string         $email = null,
                                   ?string         $phone = null,
                                   ?\DateTime      $birthdate = null,
                                   ?Address        $address = null,
                                   ?CustomerGender $gender = null,
                                   ?CustomerType   $customerType = null): self
    {
        $shipping = ShippingDataBuilder::create($name)
            ->setCpf($cpf)
            ->setPhone($phone)
            ->setEmail($email)
            ->setRg($rg)
            ->setAddress($address)
            ->setBirthdate($birthdate)
            ->setCustomerType($customerType)
            ->setGender($gender)
            ->get();

        $this->sale->shipping = $shipping;

        return $this;
    }

    public function setShippingAddressFields(string  $address,
                                             ?string $address2,
                                             string  $district,
                                             string  $city,
                                             string  $state,
                                             string  $postalcode,
                                             ?string $country = null): self
    {
        $this->sale->shipping->setAddressFields(
            $address,
            $address2,
            $district,
            $city,
            $state,
            $postalcode,
            $country
        );
        return $this;
    }
}
