<?php

declare(strict_types=1);

namespace Rentceisy\YandexMessengerBotSdk;

use Rentceisy\YandexMessengerBotSdk\Exceptions\YandexMessengerSDKException;

class YandexMessengerRequest
{
    /**
     * @throws YandexMessengerSDKException
     */
    public function __construct(
        protected ?string $accessToken,
        protected string $method,
        protected string $endpoint,
        protected array $params = [],
        protected int $timeOut = 60,
        protected int $connectTimeOut = 10,
        protected array $headers = [],
    ) {
        $this->validateAccessToken();
        $this->validateMethod();
    }

    /**
     * @throws YandexMessengerSDKException
     */
    protected function validateAccessToken(): void
    {
        if ($this->accessToken === null) {
            throw YandexMessengerSDKException::tokenNotProvided('YANDEX_MESSENGER_BOT_TOKEN');
        }
    }

    /**
     * @throws YandexMessengerSDKException
     */
    protected function validateMethod(): void
    {
        $methods = ['GET', 'POST'];

        if (!in_array(strtoupper($this->method), $methods, true)) {
            throw new YandexMessengerSDKException('Invalid HTTP method. Allowed methods: GET, POST');
        }
    }

    public function getDefaultHeaders(): array
    {
        return [
            'User-Agent' => 'YandexMessengerBotPHP/' . YandexMessenger::VERSION,
            'Authorization' => 'OAuth ' . $this->accessToken,
            'Content-Type' => 'application/json',
        ];
    }

    public function getPostParams(): array
    {
        if ($this->method !== 'POST') {
            return [];
        }

        return $this->params;
    }

    public function getHeaders(): array
    {
        return array_merge($this->getDefaultHeaders(), $this->headers);
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getTimeOut(): int
    {
        return $this->timeOut;
    }

    public function getConnectTimeOut(): int
    {
        return $this->connectTimeOut;
    }
}
