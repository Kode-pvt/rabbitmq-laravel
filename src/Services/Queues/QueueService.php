<?php

namespace KodePvt\RabbitmqLaravel\Services\Queues;

use KodePvt\RabbitmqLaravel\Contracts\Connections\ConnectionContract;
use KodePvt\RabbitmqLaravel\Contracts\Services\QueueServiceContract;
use KodePvt\RabbitmqLaravel\Facades\Connection;

class QueueService implements QueueServiceContract
{
    public function declare(string $queueName, bool $durable = false, bool $passive = false, bool $exclusive = false, bool $auto_delete = false): ?array
    {
        return Connection::getChannel()->queue_declare($queueName, false, $durable, false, false);
    }

    public function bind(string $queue, string $exchange, string|array $routingKey = ''): void
    {
        if (gettype($routingKey) === 'string') {
            Connection::getChannel()->queue_bind($queue, $exchange, $routingKey);
        } else if (gettype($routingKey) === 'array') {
            foreach ($routingKey as $key) {
                Connection::getChannel()->queue_bind($queue, $exchange, $key);
            }
        }
    }
}
