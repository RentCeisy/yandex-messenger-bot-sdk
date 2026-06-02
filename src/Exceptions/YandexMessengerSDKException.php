<?php

declare(strict_types=1);

namespace Rentceisy\YandexMessengerBotSdk\Exceptions;

use Exception;

class YandexMessengerSDKException extends Exception
{
    public static function tokenNotProvided(string $tokenEnvName): self
    {
        return new static(sprintf(
            'Required parameter "token" is not set. Pass token to YandexMessenger constructor or set %s environment variable.',
            $tokenEnvName
        ));
    }
}
