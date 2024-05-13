<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Code;

trait HasCode
{
    public function autoCode( $format, $reset = 'month', $date = '', $length = 4 ): string
    {
        if (empty($date)) $date = date('Y-m-d');

        $time = strtotime($date);

        if ($reset == 'year') {
            $key = $format .':'. date('Y', $time);
        } else {
            $key = $format .':'. date('Y', $time) .'_'. date('m', $time);
        }

        Code::updateOrCreate(
            ['key' => $key],
        )->increment('num');
        $code = Code::where('key', $key)->first();

        $replacer = [
            '{Y}' => date('Y', $time),
            '{y}' => date('y', $time),
            '{m}' => date('m', $time),
            '{d}' => date('d', $time),
            '{num}' => Str::padLeft($code->num, $length, '0'),
        ];

        return str_replace(array_keys($replacer), array_values($replacer), $format);
    }
}
