<?php

namespace KodePvt\RabbitmqLaravel\Publishers;

use KodePvt\RabbitmqLaravel\Contracts\PublisherContract;
use KodePvt\RabbitmqLaravel\Facades\Connection;
use KodePvt\RabbitmqLaravel\Services\Core\Message;

class ExchangePublisher implements PublisherContract
{
    public function basic_publish(Message $message, string $exchange = '', bool $persistent = false, string $routingKey = ''): void
    {
        $message = $message->make();

        Connection::getChannel()->basic_publish($message, $exchange, $routingKey);
    }
}
