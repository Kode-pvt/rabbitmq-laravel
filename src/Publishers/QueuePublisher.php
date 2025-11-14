<?php

namespace KodePvt\RabbitmqLaravel\Publishers;

use KodePvt\RabbitmqLaravel\Contracts\PublisherContract;
use KodePvt\RabbitmqLaravel\Facades\Connection;
use KodePvt\RabbitmqLaravel\Services\Core\Message;

class QueuePublisher implements PublisherContract
{
    public function basic_publish(Message $message, string $destination = '', bool $persistent = false, string $routingKey = ''): void
    {
        if ($destination != '' && $routingKey == '') {
            $routingKey = $destination;
            $destination = '';
        }

        Connection::getChannel()->basic_publish($message->make(), $destination, $routingKey);
    }
}
