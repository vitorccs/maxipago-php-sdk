<?php

namespace Vitorccs\Maxipago\Http;

use Vitorccs\Maxipago\Entities\AbstractSale;
use Vitorccs\Maxipago\Entities\PixSale;
use Vitorccs\Maxipago\Exceptions\MaxipagoException;
use Vitorccs\Maxipago\Exceptions\MaxipagoProcessorException;
use Vitorccs\Maxipago\Exceptions\MaxipagoRequestException;
use Vitorccs\Maxipago\Exceptions\MaxipagoValidationException;
use Vitorccs\Maxipago\Traits\TransactionRequestRoot;

class SaleService extends Resource
{
    use TransactionRequestRoot;

    /**
     * @throws MaxipagoRequestException
     * @throws MaxipagoProcessorException
     * @throws MaxipagoValidationException
     * @throws MaxipagoException
     */
    public function createSale(AbstractSale $sale,
                               bool         $checkSuccess = false): object
    {
        if ($sale instanceof PixSale) {
            return $this->createPixSale($sale, $checkSuccess);
        }

        throw new MaxipagoException('Cannot detect instance of Sale');
    }

    /**
     * @throws MaxipagoValidationException
     * @throws MaxipagoRequestException
     * @throws MaxipagoProcessorException
     */
    public function createPixSale(PixSale|array $pixSale,
                                  bool          $checkSuccess = false): object
    {
        $sale = $pixSale instanceof PixSale
            ? $pixSale->export()
            : $pixSale;

        $data = [
            'order' => [
                'sale' => $sale
            ]
        ];

        try {
            return $this->postXml($data);
        } catch (MaxipagoValidationException $e) {
            return $this->checkForProcessorException($e, $checkSuccess);
        }
    }

    /**
     * @throws MaxipagoValidationException
     * @throws MaxipagoRequestException
     */
    public function cancelSale(string $transactionId): object
    {
        $data = [
            'order' => [
                'void' => [
                    'transactionID' => $transactionId
                ]
            ]
        ];

        return $this->postXml($data);
    }

    /**
     * @throws MaxipagoValidationException
     * @throws MaxipagoRequestException
     */
    public function refundSale(string $orderId,
                               string $referenceNum,
                               float  $chargeTotal): object
    {
        $data = [
            'order' => [
                'return' => [
                    'orderID' => $orderId,
                    'referenceNum' => $referenceNum,
                    'payment' => [
                        'chargeTotal' => $chargeTotal
                    ]
                ]
            ]
        ];

        return $this->postXml($data);
    }

    /**
     * Check for API Processor errors
     *
     * Format:
     * <root>
     *   <orderID>0A0115CB:018A9C5B6ACA:DBCC:0344E731</orderID>
     *   <referenceNum>15d71318-71f3-4312-96f8-741f082dd6e7</referenceNum>
     *   <transactionID>623999551</transactionID>
     *   <transactionTimestamp>1716315187</transactionTimestamp>
     *   <responseCode>1</responseCode>
     *   <responseMessage/>
     *   <processorCode>400</processorCode>
     *   <processorMessage> - O objeto cob.devedor nao respeita o schema.</processorMessage>
     *   <processorName>PIXITAU</processorName>
     *   <errorMessage>Error sending request to pix Itau. </errorMessage>
     * </root>
     *
     * @throws MaxipagoProcessorException
     * @throws MaxipagoValidationException
     */
    private function checkForProcessorException(MaxipagoValidationException $e,
                                                bool                        $checkSuccess): object
    {
        $body = $e->getResponseBody();

        $isProcessorException = !is_null($body->orderID ?? null)
            && !is_null($body->referenceNum ?? null)
            && !is_null($body->transactionID ?? null);

        if (!$isProcessorException) {
            throw $e;
        }

        if (!$checkSuccess) {
            return $body;
        }

        throw new MaxipagoProcessorException(
            $e->getMessage(),
            $body->processorCode,
            $e->getResponseCode(),
            $e->getHttpCode(),
            $body
        );
    }
}
