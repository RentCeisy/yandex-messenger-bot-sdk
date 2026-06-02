<?php

declare(strict_types=1);

namespace Rentceisy\YandexMessengerBotSdk\Traits;

use Rentceisy\YandexMessengerBotSdk\Exceptions\YandexMessengerSDKException;
use Rentceisy\YandexMessengerBotSdk\HttpClients\GuzzleHttpClient;
use Rentceisy\YandexMessengerBotSdk\HttpClients\HttpClientInterface;
use Rentceisy\YandexMessengerBotSdk\YandexMessengerClient;
use Rentceisy\YandexMessengerBotSdk\YandexMessengerRequest;
use Rentceisy\YandexMessengerBotSdk\YandexMessengerResponse;

trait Http
{
    protected ?string $accessToken = null;

    protected ?YandexMessengerClient $client = null;

    protected ?YandexMessengerResponse $lastResponse = null;

    protected int $connectTimeOut = 10;

    protected int $timeOut = 60;

    protected ?HttpClientInterface $httpClientHandler = null;

    protected ?string $baseBotUrl = null;

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getClient(): YandexMessengerClient
    {
        if ($this->client === null) {
            $httpClientHandler = $this->httpClientHandler ?? new GuzzleHttpClient();
            $this->client = new YandexMessengerClient($httpClientHandler, $this->baseBotUrl);
        }

        return $this->client;
    }

    public function post(string $endpoint, array $params = []): YandexMessengerResponse
    {
        return $this->sendRequest('POST', $endpoint, $params);
    }

    public function sendRequest(string $method, string $endpoint, array $params = []): YandexMessengerResponse
    {
        $request = $this->resolveYandexMessengerRequest($method, $endpoint, $params);
        $this->lastResponse = $this->getClient()->sendRequest($request);

        return $this->lastResponse;
    }

    /**
     * @throws YandexMessengerSDKException
     */
    protected function resolveYandexMessengerRequest(
        string $method,
        string $endpoint,
        array $params = []
    ): YandexMessengerRequest {
        return new YandexMessengerRequest(
            $this->getAccessToken(),
            $method,
            $endpoint,
            $params,
            $this->timeOut,
            $this->connectTimeOut,
        );
    }

    public function getLastResponse(): ?YandexMessengerResponse
    {
        return $this->lastResponse;
    }

    public function setTimeOut(int $timeOut): self
    {
        $this->timeOut = $timeOut;

        return $this;
    }

    public function getTimeOut(): int
    {
        return $this->timeOut;
    }

    public function setConnectTimeOut(int $connectTimeOut): self
    {
        $this->connectTimeOut = $connectTimeOut;

        return $this;
    }

    public function getConnectTimeOut(): int
    {
        return $this->connectTimeOut;
    }

    public function setHttpClientHandler(HttpClientInterface $httpClientHandler): self
    {
        $this->httpClientHandler = $httpClientHandler;

        return $this;
    }

    public function getHttpClientHandler(): ?HttpClientInterface
    {
        return $this->httpClientHandler;
    }

    public function setBaseBotUrl(string $baseBotUrl): self
    {
        $this->baseBotUrl = $baseBotUrl;

        return $this;
    }

    public function getBaseBotUrl(): ?string
    {
        return $this->baseBotUrl;
    }
}
