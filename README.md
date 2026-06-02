# Yandex Messenger Bot SDK

PHP SDK для отправки сообщений через [Bot API Яндекс Мессенджера](https://yandex.ru/dev/messenger/doc/ru/).

## Требования

- PHP >= 8.0
- Composer

## Установка

```bash
composer require rentceisy/yandex-messenger-bot-sdk
```

## Быстрый старт

```php
<?php

require 'vendor/autoload.php';

use Rentceisy\YandexMessengerBotSdk\YandexMessenger;

$bot = new YandexMessenger('YOUR_BOT_TOKEN');

// Отправка сообщения в групповой чат
$message = $bot->sendMessage([
    'chat_id' => '0/0/4f24b544-***************',
    'text'    => 'Привет, команда!',
]);

echo $message->message_id; // 1647523230504005
```

## Метод `sendMessage`

Отправляет текстовое сообщение в групповой чат или личный диалог пользователю.

```php
public function sendMessage(array $params): ?\Rentceisy\YandexMessengerBotSdk\Objects\Message
```

### Параметры

| Параметр | Тип | Обязательный | Описание |
|---|---|---|---|
| `chat_id` | `string` | Нет* | ID группового чата. Бот должен быть участником чата |
| `login` | `string` | Нет* | Логин пользователя для отправки в личный чат |
| `text` | `string` | Нет | Текст сообщения (не более 6000 символов) |
| `payload_id` | `string` | Нет | Уникальный ID запроса. Запросы с одинаковым ID трактуются как дубликаты |
| `message_id` | `int` | Нет | ID сообщения для редактирования. Сообщение должно быть из того же чата |
| `reply_message_id` | `int` | Нет | ID сообщения, на которое будет дан ответ |
| `thread_id` | `int` | Нет | ID сообщения, под которым будет открыт тред |
| `disable_notification` | `bool` | Нет | Отключить уведомление о сообщении. По умолчанию `false` |
| `important` | `bool` | Нет | Пометить сообщение как важное. По умолчанию `false` |
| `disable_web_page_preview` | `bool` | Нет | Отключить раскрытие ссылок. По умолчанию `false` |

> \* Параметры `chat_id` и `login` являются необязательными по отдельности, но **необходимо указать хотя бы один из них**.

### Примеры

**Отправка в групповой чат:**

```php
$message = $bot->sendMessage([
    'chat_id' => '0/0/4f24b544-***************',
    'text'    => 'Привет!',
]);
```

**Отправка пользователю:**

```php
$message = $bot->sendMessage([
    'login' => 'ivan.ivanov',
    'text'  => 'Привет, Иван!',
]);
```

**Ответ на сообщение:**

```php
$message = $bot->sendMessage([
    'chat_id'          => '0/0/4f24b544-***************',
    'text'             => 'Это ответ',
    'reply_message_id' => 1647523230504005,
]);
```

**Важное сообщение без уведомления и без превью ссылок:**

```php
$message = $bot->sendMessage([
    'chat_id'                  => '0/0/4f24b544-***************',
    'text'                     => 'https://yandex.ru',
    'important'                => true,
    'disable_notification'     => true,
    'disable_web_page_preview' => true,
]);
```

**Идемпотентная отправка (защита от дубликатов):**

```php
$message = $bot->sendMessage([
    'chat_id'    => '0/0/4f24b544-***************',
    'text'       => 'Привет!',
    'payload_id' => uniqid('msg_', true),
]);
```

### Возвращаемое значение

При успешной отправке возвращает объект `Message`, содержащий:

| Свойство | Тип | Описание |
|---|---|---|
| `ok` | `bool` | Флаг успешности (`true`) |
| `message_id` | `int` | ID отправленного сообщения в чате |

```php
$message = $bot->sendMessage([...]);

echo $message->ok;          // true
echo $message->message_id;  // 1647523230504005
```

## Обработка ошибок

При ошибках API SDK выбрасывает исключения:

```php
use Rentceisy\YandexMessengerBotSdk\Exceptions\YandexMessengerResponseException;
use Rentceisy\YandexMessengerBotSdk\Exceptions\YandexMessengerSDKException;

try {
    $message = $bot->sendMessage([
        'chat_id' => '0/0/invalid',
        'text'    => 'Привет!',
    ]);
} catch (YandexMessengerResponseException $e) {
    // Ошибка от API (например: "Bot is not a member of the chat")
    echo $e->getMessage();
    echo $e->getErrorCode();
} catch (YandexMessengerSDKException $e) {
    // Ошибка SDK или HTTP-клиента
    echo $e->getMessage();
}
```

## Настройка HTTP-клиента

### Таймауты

```php
$bot->setTimeOut(30);        // Таймаут запроса (сек)
$bot->setConnectTimeOut(5);  // Таймаут соединения (сек)
```

### Кастомный HTTP-клиент

SDK использует Guzzle по умолчанию, но вы можете подменить реализацию через интерфейс `HttpClientInterface`:

```php
$bot->setHttpClientHandler($myCustomHttpClient);
```

### Кастомный Base URL

```php
$bot->setBaseBotUrl('https://custom-proxy.example.com/bot/v1');
```

## Лицензия

MIT
