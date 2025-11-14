<?php

namespace KodePvt\RabbitmqLaravel\Contracts\Services;

interface QueueServiceContract
{
    public function declare(string $queueName, bool $durable = false, bool $passive = false, bool $exclusive = false, bool $auto_delete = false): ?array;
    public function bind(string $queue, string $exchange, string $routingKey = ''): void;
}
