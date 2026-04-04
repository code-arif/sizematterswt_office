<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdImpression extends Model
{
    protected $fillable = [
        'advertisement_id',
        'user_id',
        'device_id',
        'is_dismissed',
        'seen_at',
    ];

    protected $casts = [
        'is_dismissed' => 'boolean',
        'seen_at'      => 'datetime',
    ];

    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
