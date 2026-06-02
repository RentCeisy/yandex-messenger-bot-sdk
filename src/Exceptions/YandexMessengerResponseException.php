<?php

declare(strict_types=1);

namespace Rentceisy\YandexMessengerBotSdk\Exceptions;

use Rentceisy\YandexMessengerBotSdk\YandexMessengerResponse;

class YandexMessengerResponseException extends YandexMessengerSDKException
{
    protected ?YandexMessengerResponse $response;

    protected int $errorCode;

    public function __construct(
        YandexMessengerResponse $response,
        string $message,
        int $code,
        ?YandexMessengerSDKException $previousException = null
    ) {
        $this->response = $response;
        $this->errorCode = $code;

        parent::__construct($message, $code, $previousException);
    }

    public static function create(YandexMessengerResponse $response): self
    {
        $data = $response->getDecodedBody();
        $message = $data['description'] ?? 'Unknown error';
        $code = (int) ($data['error_code'] ?? 0);

        return new static($response, $message, $code);
    }

    public function getResponse(): ?YandexMessengerResponse
    {
        return $this->response;
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }
}
