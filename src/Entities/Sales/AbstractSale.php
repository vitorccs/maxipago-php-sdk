<?php

namespace Vitorccs\Maxipago\Entities\Sales;

use JsonSerializable;
use Vitorccs\Maxipago\Entities\Exportable;
use Vitorccs\Maxipago\Entities\PayTypes\AbstractPayType;
use Vitorccs\Maxipago\Entities\Sales\Sections\BillingData;
use Vitorccs\Maxipago\Entities\Sales\Sections\Payment;
use Vitorccs\Maxipago\Entities\Sales\Sections\ShippingData;

abstract class AbstractSale implements JsonSerializable
{
    use Exportable;

    public int $processorID;
    public string $referenceNum;
    public Payment $payment;
    public ?BillingData $billing = null;
    public ?string $ipAddress = null;
    public ?string $fraudCheck = null;
    public ?string $customerIdExt = null;
    public ?ShippingData $shipping = null;
    protected AbstractPayType $payType;

    public function __construct(AbstractPayType $payType,
                                Payment         $payment,
                                string          $referenceNum,
                                int             $processorID)
    {
        $this->payment = $payment;
        $this->referenceNum = $referenceNum;
        $this->processorID = $processorID;
        $this->payType = $payType;
    }

    public function getPayType(): AbstractPayType
    {
        return $this->payType;
    }

    public function nonExportableFields(): array
    {
        return [
            'payType'
        ];
    }

    public function addExportableFields(): array
    {
        return [
            'transactionDetail' => [
                'payType' => [
                    $this->payType->nodeName() => (array)$this->payType
                ]
            ]
        ];
    }
}
