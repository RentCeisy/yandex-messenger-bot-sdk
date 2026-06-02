<?php

declare(strict_types=1);

namespace Rentceisy\YandexMessengerBotSdk;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Rentceisy\YandexMessengerBotSdk\Exceptions\YandexMessengerOtherException;
use Rentceisy\YandexMessengerBotSdk\Exceptions\YandexMessengerResponseException;
use Rentceisy\YandexMessengerBotSdk\Exceptions\YandexMessengerSDKException;

class YandexMessengerResponse
{
    protected ?int $httpStatusCode;

    protected array $headers;

    protected string $body;

    protected mixed $decodedBody;

    protected string $endPoint;

    protected ?YandexMessengerSDKException $thrownException = null;

    public function __construct(
        protected YandexMessengerRequest $request,
        ResponseInterface|PromiseInterface|null $response
    ) {
        if ($response instanceof PromiseInterface) {
            $this->httpStatusCode = null;
        } elseif ($response instanceof ResponseInterface) {
            $this->httpStatusCode = $response->getStatusCode();
            $this->body = $response->getBody()->getContents();
            $this->headers = $response->getHeaders();
        } else {
            $this->httpStatusCode = null;
            $this->body = '';
            $this->headers = [];
        }

        $this->endPoint = $request->getEndpoint();
        $this->decodeBody();
    }

    protected function decodeBody(): void
    {
        $this->decodedBody = json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($this->decodedBody)) {
            $this->decodedBody = [];
            parse_str($this->body, $this->decodedBody);
        }

        if ($this->isError()) {
            $this->makeException();
        }
    }

    public function isError(): bool
    {
        return isset($this->decodedBody['ok']) && $this->decodedBody['ok'] === false;
    }

    protected function makeException(): void
    {
        $this->thrownException = YandexMessengerResponseException::create($this);
    }

    public function getResult(): mixed
    {
        return $this->decodedBody['result'] ?? false;
    }

    public function getDecodedBody(): array
    {
        return $this->decodedBody ?? [];
    }

    public function getRawResponse(): string
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode;
    }

    public function getEndPoint(): string
    {
        return $this->endPoint;
    }

    public function throwException(): void
    {
        throw $this->thrownException;
    }

    public function getThrownException(): ?YandexMessengerSDKException
    {
        return $this->thrownException;
    }
}
