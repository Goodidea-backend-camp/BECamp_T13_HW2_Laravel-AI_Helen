<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageGenerationMessage extends Model
{
    use HasFactory;

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }
}
