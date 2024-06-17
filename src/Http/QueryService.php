<?php

namespace Vitorccs\Maxipago\Http;

use Vitorccs\Maxipago\Exceptions\MaxipagoRequestException;
use Vitorccs\Maxipago\Exceptions\MaxipagoValidationException;
use Vitorccs\Maxipago\Traits\RapiRequestRoot;

class QueryService extends Resource
{
    use RapiRequestRoot;

    /**
     * @throws MaxipagoRequestException
     * @throws MaxipagoValidationException
     */
    public function getByOrderId(string $orderId): array
    {
        $payload = [
            'request' => [
                'filterOptions' => [
                    'orderId' => $orderId
                ]
            ]
        ];

        $response = $this->reportsApi($payload, 'transactionDetailReport');

        return $this->convertResponseToArray($response);
    }

    /**
     * @throws MaxipagoValidationException
     * @throws MaxipagoRequestException
     */
    public function getLastByOrderId(string $orderId): ?object
    {
        $records = $this->getByOrderId($orderId);

        return count($records) ? $records[0] : null;
    }

    /**
     * @throws MaxipagoRequestException
     * @throws MaxipagoValidationException
     */
    public function getByReferenceNumber(string $referenceNum): array
    {
        $payload = [
            'request' => [
                'filterOptions' => [
                    'referenceNum' => $referenceNum
                ]
            ]
        ];

        $response = $this->reportsApi($payload, 'transactionDetailReport');

        return $this->convertResponseToArray($response);
    }

    /**
     * @throws MaxipagoRequestException
     * @throws MaxipagoValidationException
     */
    public function getByTransactionId(string $transactionId): ?object
    {
        $payload = [
            'request' => [
                'filterOptions' => [
                    'transactionId' => $transactionId
                ]
            ]
        ];

        $response = $this->reportsApi($payload, 'transactionDetailReport');

        return $response->result->records->record ?? null;
    }

    /**
     * Force response to array format
     *
     * NOTE: due to XML limitations, when there is only one <record> inside <records>,
     * the XML Conversor recognizes both as an Object each. However, with multiple
     * <record>, it is then correctly interpreted as an Array.
     */
    private function convertResponseToArray(object $response): array
    {
        $records = $response->result->records->record ?? [];

        // wrap object into an array
        $records = is_array($records) ? $records : [$records];

        // remove empty or null values from array
        $records = array_filter($records);

        return $records;
    }
}
