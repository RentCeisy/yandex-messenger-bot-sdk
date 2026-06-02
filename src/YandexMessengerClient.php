<?php

declare(strict_types=1);

namespace Rentceisy\YandexMessengerBotSdk;

use Rentceisy\YandexMessengerBotSdk\Exceptions\YandexMessengerResponseException;
use Rentceisy\YandexMessengerBotSdk\Exceptions\YandexMessengerSDKException;
use Rentceisy\YandexMessengerBotSdk\HttpClients\HttpClientInterface;

class YandexMessengerClient
{
    public const BASE_BOT_URL = 'https://botapi.messenger.yandex.net/bot/v1';

    protected HttpClientInterface $httpClientHandler;

    protected ?string $baseBotUrl = null;

    public function __construct(HttpClientInterface $httpClientHandler, ?string $baseBotUrl = null)
    {
        $this->httpClientHandler = $httpClientHandler;
        $this->baseBotUrl = $baseBotUrl ?? static::BASE_BOT_URL;
    }

    /**
     * @throws YandexMessengerSDKException
     */
    public function sendRequest(YandexMessengerRequest $request): YandexMessengerResponse
    {
        [$url, $method, $headers] = $this->prepareRequest($request);

        $options = $this->getOptions($request, $method);

        $rawResponse = $this->httpClientHandler->send($url, $method, $headers, $options);

        $response = new YandexMessengerResponse($request, $rawResponse);

        if ($response->isError()) {
            throw $response->getThrownException();
        }

        return $response;
    }

    protected function prepareRequest(YandexMessengerRequest $request): array
    {
        $url = $this->baseBotUrl . '/' . $request->getEndpoint();

        return [$url, $request->getMethod(), $request->getHeaders()];
    }

    protected function getOptions(YandexMessengerRequest $request, string $method): array
    {
        if ($method === 'POST') {
            return [
                'body' => json_encode($request->getPostParams()),
            ];
        }

        return [
            'body' => http_build_query($request->getParams()),
        ];
    }
}
