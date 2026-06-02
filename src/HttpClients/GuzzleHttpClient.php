<?php

declare(strict_types=1);

namespace Rentceisy\YandexMessengerBotSdk\HttpClients;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Rentceisy\YandexMessengerBotSdk\Exceptions\YandexMessengerSDKException;

class GuzzleHttpClient implements HttpClientInterface
{
    protected static array $promises = [];

    protected ClientInterface $client;

    protected int $timeOut = 60;

    protected int $connectTimeOut = 10;

    public function __construct(?ClientInterface $client = null)
    {
        $this->client = $client ?? new Client();
    }

    /**
     * @throws \Throwable
     */
    public function __destruct()
    {
        Utils::unwrap(self::$promises);
    }

    /**
     * @throws YandexMessengerSDKException
     */
    public function send(
        string $url,
        string $method,
        array $headers = [],
        array $options = [],
        bool $isAsyncRequest = false
    ): ResponseInterface|PromiseInterface|null {
        $request = new Request($method, $url, $headers);

        try {
            $response = $this->client->requestAsync($method, $url, $this->getOptions($request, $options));

            if ($isAsyncRequest) {
                self::$promises[] = $response;
            } else {
                $response = $response->wait();
            }
        } catch (RequestException $e) {
            $response = $e->getResponse();

            if (!$response instanceof ResponseInterface) {
                throw new YandexMessengerSDKException($e->getMessage(), $e->getCode());
            }
        } catch (GuzzleException $e) {
            throw new YandexMessengerSDKException($e->getMessage(), $e->getCode());
        }

        return $response;
    }

    protected function getOptions(Request $request, array $options): array
    {
        return [
            'headers' => $request->getHeaders(),
            'body' => $options['body'] ?? null,
            'timeout' => $this->getTimeOut(),
            'connect_timeout' => $this->getConnectTimeOut(),
            'synchronous' => true,
        ];
    }

    public function getTimeOut(): int
    {
        return $this->timeOut;
    }

    public function setTimeOut(int $timeOut): self
    {
        $this->timeOut = $timeOut;

        return $this;
    }

    public function getConnectTimeOut(): int
    {
        return $this->connectTimeOut;
    }

    public function setConnectTimeOut(int $connectTimeOut): self
    {
        $this->connectTimeOut = $connectTimeOut;

        return $this;
    }
}
