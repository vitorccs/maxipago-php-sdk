<?php

namespace Vitorccs\Maxipago\Entities;

use Vitorccs\Maxipago\Exceptions\MaxipagoParameterException;

class Parameters
{
    /**
     * The ENV name for Merchant ID
     */
    const MAXIPAGO_MERCHANT_ID = 'MAXIPAGO_MERCHANT_ID';

    /**
     * The ENV name for Merchant Key
     */
    const MAXIPAGO_MERCHANT_KEY = 'MAXIPAGO_MERCHANT_KEY';

    /**
     * The ENV name for toggling Sandbox mode
     */
    const MAXIPAGO_SANDBOX = 'MAXIPAGO_SANDBOX';

    /**
     * The ENV name for HTTP Timeout parameter
     */
    const MAXIPAGO_TIMEOUT = 'MAXIPAGO_TIMEOUT';

    /**
     * The default API timeout
     */
    const DEFAULT_TIMEOUT = 30;

    /**
     * The default API mode
     */
    const DEFAULT_SANDBOX = false;

    /**
     * The Merchant ID
     */
    protected ?string $merchantId;

    /**
     * The Merchant Key
     */
    private ?string $merchantKey;

    /**
     * The HTTP timeout
     */
    protected ?int $timeout;

    /**
     * The toggle for Sandbox mode
     */
    protected ?bool $sandbox;

    /**
     * @throws MaxipagoParameterException
     */
    public function __construct(?string $merchantId = null,
                                ?string $merchantKey = null,
                                ?bool   $sandbox = null,
                                ?int    $timeout = null)
    {
        $this->setMerchantId($merchantId);
        $this->setMerchantKey($merchantKey);
        $this->setSandbox($sandbox);
        $this->setTimeout($timeout);
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function getMerchantKey(): string
    {
        return $this->merchantKey;
    }

    public function getSandbox(): bool
    {
        return $this->sandbox;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @throws MaxipagoParameterException
     */
    public function setMerchantId(?string $merchantId): void
    {
        $merchantId = $merchantId ?: getenv(static::MAXIPAGO_MERCHANT_ID) ?: null;

        if (empty($merchantId)) {
            throw new MaxipagoParameterException("Missing required parameter '" . static::MAXIPAGO_MERCHANT_ID . "'");
        }

        $this->merchantId = $merchantId;
    }

    /**
     * @throws MaxipagoParameterException
     */
    public function setMerchantKey(?string $merchantKey): void
    {
        $merchantKey = $merchantKey ?: getenv(static::MAXIPAGO_MERCHANT_KEY) ?: null;

        if (empty($merchantKey)) {
            throw new MaxipagoParameterException("Missing required parameter '" . static::MAXIPAGO_MERCHANT_KEY . "'");
        }

        $this->merchantKey = $merchantKey;
    }

    /**
     * @throws MaxipagoParameterException
     */
    public function setSandbox(?bool $sandbox): void
    {
        $envValue = strtolower(getenv(static::MAXIPAGO_SANDBOX));

        if (strlen($envValue) && !in_array($envValue, ['true', 'false'])) {
            throw new MaxipagoParameterException("Invalid parameter value for '" . static::MAXIPAGO_SANDBOX . "'");
        }

        if (is_null($sandbox) && strlen($envValue)) {
            $sandbox = $envValue === 'true';
        }

        if (is_null($sandbox)) {
            $sandbox = self::DEFAULT_SANDBOX;
        }

        $this->sandbox = $sandbox;
    }

    /**
     * @throws MaxipagoParameterException
     */
    public function setTimeout(int $timeout = null): void
    {
        $envValue = getenv(static::MAXIPAGO_TIMEOUT);

        if (strlen($envValue) && !is_numeric($envValue)) {
            throw new MaxipagoParameterException("Invalid parameter value for '" . static::MAXIPAGO_TIMEOUT . "'");
        }

        if (is_null($timeout) && strlen($envValue)) {
            $timeout = intval($envValue);
        }

        if (is_null($timeout)) {
            $timeout = static::DEFAULT_TIMEOUT;
        }

        $this->timeout = $timeout;
    }
}
