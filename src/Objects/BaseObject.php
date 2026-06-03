<?php

declare(strict_types=1);

namespace Rentceisy\YandexMessengerBotSdk\Objects;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class BaseObject extends Collection
{
    public function __construct(array $data)
    {
        parent::__construct($this->getPropertyValue($data));
    }

    public static function make($items = []): static
    {
        $data = data_get($items, 'result', $items);
        $data = is_array($data) ? $data : (array) $data;

        return new static($data);
    }

    public function __get($key)
    {
        return $this->get(Str::snake($key));
    }

    public function __set($key, $value)
    {
        throw new \RuntimeException('YandexMessenger objects are immutable. Cannot set property: ' . $key);
    }

    abstract public function relations(): array;

    protected function getPropertyValue(mixed $property): mixed
    {
        if (!is_array($property)) {
            return $property;
        }

        if (isset($property['result'])) {
            $property = $property['result'];
        }

        $relations = $this->relations();

        return collect($property)
            ->map(function ($value, $key) use ($relations) {
                if (isset($relations[$key])) {
                    return $this->getRelationValue($relations[$key], $value);
                }

                if (is_array($value)) {
                    $class = 'Rentceisy\\YandexMessengerBotSdk\\Objects\\' . Str::studly($key);

                    if (class_exists($class)) {
                        return $this->getRelationValue($class, $value);
                    }

                    return YandexMessengerObject::make($value);
                }

                return $value;
            })
            ->all();
    }

    protected function getRelationValue(string $class, mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        if (is_array(reset($value))) {
            return collect($value)->map(fn ($item) => new $class($item))->all();
        }

        return new $class($value);
    }

    public function get($key, $default = null): mixed
    {
        $value = parent::get($key, $default);

        if (is_array($value)) {
            return $this->getPropertyValue($value);
        }

        return $value;
    }

    public function getStatus(): bool
    {
        return $this->items['ok'] ?? false;
    }

    public function isType(string $type): bool
    {
        return isset($this->items[$type]) || $this->detectType() === $type;
    }

    public function objectType(): string
    {
        return $this->detectType();
    }

    protected function detectType(): string
    {
        return 'object';
    }

    public function __call($method, $parameters)
    {
        $action = substr($method, 0, 3);

        if ($action === 'get') {
            $property = Str::snake(substr($method, 3));

            return $this->get($property);
        }

        return parent::__call($method, $parameters);
    }
}
