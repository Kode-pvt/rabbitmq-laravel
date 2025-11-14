<?php

namespace KodePvt\RabbitmqLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static self fromArray(array $config)
 * @method static \PhpAmqpLib\Channel\AMQPChannel getChannel()
 * @method static void closeConnection()
 * @method static void closeChannel()
 */

class Connection extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'connection';
    }
}
