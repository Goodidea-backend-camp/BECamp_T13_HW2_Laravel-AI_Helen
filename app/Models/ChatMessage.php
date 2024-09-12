<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatMessage extends Model
{
    use HasFactory;

    public const ROLE_USER = 1;

    public const ROLE_AI = 2;

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function speechFile(): HasOne
    {
        return $this->hasOne(SpeechFile::class);
    }
}
