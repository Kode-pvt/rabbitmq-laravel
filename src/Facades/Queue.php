<?php

namespace KodePvt\RabbitmqLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static ?array declare(string $queueName, bool $durable = false, bool $passive = false, bool $exclusive = false, bool $auto_delete = false)
 * @method static void bind(string $queue, string $exchange, string $routingKey = '')
 */
class Queue extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rabbit-queue';
    }
}
