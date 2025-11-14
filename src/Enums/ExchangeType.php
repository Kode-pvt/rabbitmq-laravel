<?php

namespace KodePvt\RabbitmqLaravel\Enums;

enum ExchangeType: string
{
    case FANOUT = 'fanout';
    case DIRECT = 'direct';
    case TOPIC = 'topic';
    case HEADERS = 'headers';
}
