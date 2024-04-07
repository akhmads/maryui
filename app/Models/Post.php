<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $guarded = ['id'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class,'author_id','id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Language::class);
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
