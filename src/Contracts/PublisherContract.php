<?php

namespace KodePvt\RabbitmqLaravel\Contracts;

use KodePvt\RabbitmqLaravel\Services\Core\Message;

interface PublisherContract
{
    public function basic_publish(Message $message, string $destination = '', bool $persistent = false, string $routingKey = ''): void;
}
