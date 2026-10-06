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

    protected AbstractPayType $payType;
    public Payment $payment;
    public string $referenceNum;
    public ?int $processorId = null;
    public ?BillingData $billing = null;
    public ?string $ipAddress = null;
    public ?string $fraudCheck = null;
    public ?string $customerIdExt = null;
    public ?ShippingData $shipping = null;

    public function __construct(AbstractPayType $payType,
                                Payment         $payment,
                                string          $referenceNum)
    {
        $this->payType = $payType;
        $this->payment = $payment;
        $this->referenceNum = $referenceNum;
    }

    public function getPayType(): AbstractPayType
    {
        return $this->payType;
    }

    public function nonExportableFields(): array
    {
        return [
            'processorId',
            'payType'
        ];
    }

    public function addExportableFields(): array
    {
        return [
            'processorID' => $this->processorId,
            'transactionDetail' => [
                'payType' => [
                    $this->payType->nodeName() => (array)$this->payType
                ]
            ]
        ];
    }
}
