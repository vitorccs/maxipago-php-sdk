<?php

namespace Vitorccs\Maxipago\Http;

use Vitorccs\Maxipago\Entities\Parameters;
use Vitorccs\Maxipago\Exceptions\MaxipagoRequestException;
use Vitorccs\Maxipago\Exceptions\MaxipagoValidationException;
use Vitorccs\Maxipago\Http\Factories\GuzzleClientFactory;

abstract class Resource
{
    protected Api $api;

    abstract protected function root(): string;

    abstract protected function apiVersion(): ?string;

    public function __construct(?Parameters $parameters = null)
    {
        $client = GuzzleClientFactory::create($parameters);

        $this->api = new Api($client, $this->root());
    }

    /**
     * @throws MaxipagoRequestException
     * @throws MaxipagoValidationException
     */
    protected function postXml(array   $data,
                               ?string $command = null): object|array|null
    {
        return $this->api->postRequest(
            '/UniversalAPI/postXML',
            $data,
            $command,
            $this->apiVersion()
        );
    }

    /**
     * @throws MaxipagoRequestException
     * @throws MaxipagoValidationException
     */
    protected function postApi(array   $data,
                               ?string $command = null): object|array|null
    {
        return $this->api->postRequest(
            '/UniversalAPI/postAPI',
            $data,
            $command,
            $this->apiVersion()
        );
    }

    /**
     * @throws MaxipagoRequestException
     * @throws MaxipagoValidationException
     */
    protected function reportsApi(array   $data,
                                  ?string $command = null): object|array|null
    {
        return $this->api->postRequest(
            '/ReportsAPI/servlet/ReportsAPI',
            $data,
            $command,
            $this->apiVersion()
        );
    }
}
