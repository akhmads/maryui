<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\ActiveStatus;

class Contact extends Model
{
    protected $table = 'contacts';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => ActiveStatus::class,
        ];
    }
}
