<?php

namespace App\Enums;

enum ActiveStatus: string
{
    case active = 'active';
    case inactive = 'inactive';

    public function color(): string
    {
        return match($this)
        {
            self::active => 'badge-success text-white',
            self::inactive => 'badge-error text-white',
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
