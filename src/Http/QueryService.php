<?php

namespace Vitorccs\Maxipago\Http;

use Vitorccs\Maxipago\Exceptions\MaxipagoNotFoundException;
use Vitorccs\Maxipago\Exceptions\MaxipagoRequestException;
use Vitorccs\Maxipago\Exceptions\MaxipagoValidationException;
use Vitorccs\Maxipago\Traits\RapiRequestRoot;

class QueryService extends Resource
{
    use RapiRequestRoot;

    /**
     * @throws MaxipagoValidationException
     * @throws MaxipagoRequestException
     * @throws MaxipagoNotFoundException
     */
    public function getLastByOrderId(string $orderId,
                                     bool   $checkSuccess = false): ?object
    {
        $records = $this->getByOrderId($orderId, $checkSuccess);

        return count($records) ? $records[0] : null;
    }

    /**
     * @throws MaxipagoRequestException
     * @throws MaxipagoValidationException
     * @throws MaxipagoNotFoundException
     */
    public function getByOrderId(string $orderId,
                                 bool   $checkSuccess = false): array
    {
        $payload = [
            'request' => [
                'filterOptions' => [
                    'orderId' => $orderId
                ]
            ]
        ];

        $response = $this->reportsApi($payload, 'transactionDetailReport');
        $transactions = $this->convertResponseToArray($response);

        $this->checkForNotFoundException($transactions, $checkSuccess, $response);

        return $transactions;
    }

    /**
     * @throws MaxipagoRequestException
     * @throws MaxipagoValidationException
     * @throws MaxipagoNotFoundException
     */
    public function getByReferenceNumber(string $referenceNum,
                                         bool   $checkSuccess = false): array
    {
        $payload = [
            'request' => [
                'filterOptions' => [
                    'referenceNum' => $referenceNum
                ]
            ]
        ];

        $response = $this->reportsApi($payload, 'transactionDetailReport');
        $transactions = $this->convertResponseToArray($response);

        $this->checkForNotFoundException($transactions, $checkSuccess, $response);

        return $transactions;
    }

    /**
     * @throws MaxipagoRequestException
     * @throws MaxipagoValidationException
     * @throws MaxipagoNotFoundException
     */
    public function getByTransactionId(string $transactionId,
                                       bool   $checkSuccess = false): ?object
    {
        $payload = [
            'request' => [
                'filterOptions' => [
                    'transactionId' => $transactionId
                ]
            ]
        ];

        $response = $this->reportsApi($payload, 'transactionDetailReport');
        $transaction = $response->result->records->record ?? null;

        $this->checkForNotFoundException($transaction, $checkSuccess, $response);

        return $transaction;
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

    /**
     * @throws MaxipagoNotFoundException
     */
    private function checkForNotFoundException(array|object|null $transactions,
                                               bool              $checkSuccess,
                                               object            $response): void
    {
        if (empty($transactions) && $checkSuccess) {
            throw new MaxipagoNotFoundException($response);
        }
    }
}
