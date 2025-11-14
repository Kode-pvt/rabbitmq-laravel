<?php

namespace KodePvt\RabbitmqLaravel\Contracts\Services;

use KodePvt\RabbitmqLaravel\Enums\ExchangeType;

interface ExchangeServiceContract
{
    public function declare(string $name, ExchangeType $exchange): ?array;
}
