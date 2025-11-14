<?php

namespace KodePvt\RabbitmqLaravel\Contracts\Connections;

use PhpAmqpLib\Channel\AMQPChannel;

interface ConnectionContract
{
    public static function fromArray(array $config): self;
    public function getChannel(): AMQPChannel;
    public function closeConnection(): void;
    public function closeChannel(): void;
}
