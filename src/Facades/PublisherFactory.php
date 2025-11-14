<?php

namespace KodePvt\RabbitmqLaravel\Facades;

use Illuminate\Support\Facades\Facade;


/**
 * @method static \KodePvt\RabbitmqLaravel\Contracts\PublisherContract make(string $type)
 */
class PublisherFactory extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rabbit-publisher-factory';
    }
}
