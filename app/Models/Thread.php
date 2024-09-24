<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thread extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TYPE_CHAT = 1;

    public const TYPE_IMAGE = 2;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function imageGenerationMessages(): HasMany
    {
        return $this->hasMany(ImageGenerationMessage::class);
    }
}
