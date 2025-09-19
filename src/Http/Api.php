<?php

namespace Vitorccs\Maxipago\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Vitorccs\Maxipago\Constants\Config;
use Vitorccs\Maxipago\Enums\ResponseCode;
use Vitorccs\Maxipago\Exceptions\MaxipagoRequestException;
use Vitorccs\Maxipago\Exceptions\MaxipagoValidationException;

class Api
{
    private Client $client;

    private string $root;

    const ERROR_CODE_SUCCESS = 0;

    public function __construct(Client $client,
                                string $root)
    {
        $this->client = $client;
        $this->root = $root;
    }

    /**
     * @throws MaxipagoValidationException
     * @throws MaxipagoRequestException
     */
    public function postRequest(string  $endpoint,
                                array   $data,
                                ?string $command = null,
                                ?string $apiVersion = null): ?object
    {
        if (!empty($command)) {
            $data['command'] = $command;
        }

        return $this->request('POST', $endpoint, [
            Config::OPTION_XML_BODY => [$this->root => $data],
            Config::OPTION_XML_API_VERSION => $apiVersion
        ]);
    }

    /**
     * @throws MaxipagoValidationException
     * @throws MaxipagoRequestException
     */
    private function request(string $method,
                             string $endpoint = null,
                             array  $options = []): ?object
    {
        try {
            $response = $this->client->request($method, $endpoint, $options);
        } catch (RequestException $e) {
            if (!$e->hasResponse()) {
                throw new MaxipagoRequestException($e->getMessage());
            }

            $response = $e->getResponse();
        } catch (GuzzleException $e) {
            throw new MaxipagoRequestException($e->getMessage());
        }

        return $this->response($response);
    }

    /**
     * @throws MaxipagoValidationException
     * @throws MaxipagoRequestException
     */
    private function response(ResponseInterface $response): ?object
    {
        $contents = $response->getBody()->getContents();

        $body = json_decode($contents);

        $this->checkForErrors($response, $body);

        return $body;
    }

    /**
     * @throws MaxipagoValidationException
     * @throws MaxipagoRequestException
     */
    private function checkForErrors(ResponseInterface $response,
                                    ?object           $body): void
    {
        $this->checkErrorCode($response, $body);
        $this->checkResponseCode($response, $body);
        $this->checkForRequestException($response, $body);
    }

    /**
     * Check for error in "errorCode"
     *
     * Formats:
     * <root>
     *   <header>
     *     <errorCode>1</errorCode>
     *     <errorMsg><![CDATA[period is required]]></errorMsg>
     *   </header>
     * </root>
     *
     * <root>
     *   <errorCode>1</errorCode>
     *   <errorMsg>
     *     <![CDATA[Schema validation ...]]>
     *   </errorMsg>
     * </root>
     *
     * @throws MaxipagoValidationException
     */
    private function checkErrorCode(ResponseInterface $response,
                                    ?object           $body): void
    {
        $bodyHeader = $body->header ?? null;
        $errorCode = $body->errorCode
            ?? $bodyHeader->errorCode
            ?? null;
        $errorFound = is_numeric($errorCode)
            && intval($errorCode) !== self::ERROR_CODE_SUCCESS;

        if (!$errorFound) return;

        $errorMessage = $body->errorMsg
            ?? $bodyHeader->errorMsg
            ?? null;

        throw new MaxipagoValidationException(
            $errorMessage,
            $errorCode,
            null,
            $response->getStatusCode(),
            $body
        );
    }

    /**
     * Check for error in "responseCode"
     *
     * <root>
     *   <authCode/>
     *   <orderID/>
     *   <referenceNum/>
     *   <transactionID/>
     *   <responseCode>1024</responseCode>
     *   <responseMessage>INVALID REQUEST</responseMessage>
     *   <errorMessage>Request is invalid and can not be processed.</errorMessage>
     *   <processorCode/>
     *   <processorMessage/>
     * </root>
     *
     * @throws MaxipagoValidationException
     */
    private function checkResponseCode(ResponseInterface $response,
                                       ?object           $body): void
    {
        $responseCode = $body->responseCode ?? null;
        $errorFound = is_numeric($responseCode)
            && intval($responseCode) !== ResponseCode::APPROVED->value;

        if (!$errorFound) return;

        $responseMessage = $body->responseMessage ?? null;
        $errorMessage = $body->errorMessage ?? null;

        $message = !empty($responseMessage) && !empty($errorMessage)
            ? sprintf("%s (%s)", $responseMessage, $errorMessage)
            : ($responseMessage ?: $errorMessage);

        throw new MaxipagoValidationException(
            $message,
            null,
            $responseCode,
            $response->getStatusCode(),
            $body
        );
    }

    /**
     * Generic Client or Server errors
     *
     * @throws MaxipagoRequestException
     */
    private function checkForRequestException(ResponseInterface $response,
                                              ?object           $body): void
    {
        $reason = $response->getReasonPhrase();
        $httpCode = $response->getStatusCode();
        $statusClass = (int)($httpCode / 100);

        if ($statusClass !== 4 && $statusClass !== 5) return;

        throw new MaxipagoRequestException($reason, $httpCode, $body);
    }
}
