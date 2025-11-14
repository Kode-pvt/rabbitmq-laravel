<?php

namespace KodePvt\RabbitmqLaravel\Services\Exchanges;

use KodePvt\RabbitmqLaravel\Contracts\Services\ExchangeServiceContract;
use KodePvt\RabbitmqLaravel\Enums\ExchangeType;
use KodePvt\RabbitmqLaravel\Facades\Connection;
use PhpAmqpLib\Channel\AMQPChannel;

class ExchangeService implements ExchangeServiceContract
{
    public function __construct() {}

    public function declare(string $name, ExchangeType $type): ?array
    {
        return Connection::getChannel()->exchange_declare($name, $type->value, false, false, false);
    }
}
