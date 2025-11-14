<?php

namespace KodePvt\RabbitmqLaravel\Factories;

use InvalidArgumentException;
use KodePvt\RabbitmqLaravel\Contracts\PublisherContract;
use KodePvt\RabbitmqLaravel\Publishers\ExchangePublisher;
use KodePvt\RabbitmqLaravel\Publishers\QueuePublisher;

class PublisherFactory
{
    public function make(string $type): PublisherContract
    {
        return match ($type) {
            'queue' => new QueuePublisher,
            'exchange' => new ExchangePublisher,
            default => throw new InvalidArgumentException("Unknown publisher type [$type]")
        };
    }
}
