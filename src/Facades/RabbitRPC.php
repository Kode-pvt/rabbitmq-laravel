<?php

namespace KodePvt\RabbitmqLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $route, callable $callback)
 */

class RabbitRPC extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rabbit-router';
    }
}
