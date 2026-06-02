<?php

declare(strict_types=1);

namespace Rentceisy\YandexMessengerBotSdk;

use Rentceisy\YandexMessengerBotSdk\Exceptions\YandexMessengerSDKException;
use Rentceisy\YandexMessengerBotSdk\Methods\Message;
use Rentceisy\YandexMessengerBotSdk\Traits\Http;

class YandexMessenger
{
    use Http;
    use Message;

    public const VERSION = 'v0.0.1';

    /**
     * @throws YandexMessengerSDKException
     */
    public function __construct(?string $token = null)
    {
        $this->accessToken = $token;

        if ($this->accessToken !== null) {
            $this->validateAccessToken();
        }
    }

    /**
     * @throws YandexMessengerSDKException
     */
    protected function validateAccessToken(): void
    {
        if ($this->accessToken === '' || $this->accessToken === '0') {
            throw YandexMessengerSDKException::tokenNotProvided('YANDEX_MESSENGER_BOT_TOKEN');
        }
    }
}
