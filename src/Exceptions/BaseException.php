<?php


namespace KodePvt\RabbitmqLaravel\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    public function __construct(
        $message = "",
        $replace = null
    ) {
        if ($replace) str_replace('?', $replace, $message);
        $this->message = $message;
    }
}
