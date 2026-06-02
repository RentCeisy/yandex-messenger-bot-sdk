<?php

declare(strict_types=1);

namespace Rentceisy\YandexMessengerBotSdk\HttpClients;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface
{
    public function send(
        string $url,
        string $method,
        array $headers = [],
        array $options = [],
        bool $isAsyncRequest = false
    ): ResponseInterface|PromiseInterface|null;

    public function getTimeOut(): int;

    public function setTimeOut(int $timeOut): self;

    public function getConnectTimeOut(): int;

    public function setConnectTimeOut(int $connectTimeOut): self;
}
