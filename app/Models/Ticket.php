<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = [];
    //o

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany {
        return $this->hasMany(Reply::class)->orderBy('created_at','desc');
    }
}
