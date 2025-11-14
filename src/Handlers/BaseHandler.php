<?php

namespace KodePvt\RabbitmqLaravel\Handlers;

use KodePvt\RabbitmqLaravel\Exceptions\MethodNotFoundException;

abstract class BaseHandler
{
    public static function __callStatic($name, $arguments)
    {
        $instance = new static();
        if (method_exists($instance, $name)) {
            return $instance->$name(...$arguments);
        }
        throw new MethodNotFoundException("$name: does not exists");
    }
}
