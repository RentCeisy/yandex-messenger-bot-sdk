<?php

declare(strict_types=1);

namespace Rentceisy\YandexMessengerBotSdk\Methods;

use Rentceisy\YandexMessengerBotSdk\Objects\Message as MessageObject;

/**
 * @mixin \Rentceisy\YandexMessengerBotSdk\YandexMessenger
 */
trait Message
{
    public function sendMessage(array $params): ?MessageObject
    {
        if ($this->getAccessToken() === null) {
            return null;
        }

        $response = $this->post('messages/sendText/', $params);

        return new MessageObject($response->getDecodedBody());
    }
}
