<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'hour',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
