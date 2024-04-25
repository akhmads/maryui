<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\ActiveStatus;

class Item extends Model
{
    protected $table = 'items';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => ActiveStatus::class,
        ];
    }
}
