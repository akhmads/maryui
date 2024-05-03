<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case open = 'open';
    case close = 'close';
    case void = 'void';

    public function color(): string
    {
        return match($this)
        {
            self::open => 'badge-info text-white',
            self::close => 'badge-success text-white',
            self::void => 'badge-error text-white',
        };
    }

    public static function toSelect(): array
    {
        $array = [];
        foreach (self::cases() as $key => $case) {
            $array[$key]['id'] = $case->value;
            $array[$key]['name'] = $case->name;
        }
        return $array;
    }
}
