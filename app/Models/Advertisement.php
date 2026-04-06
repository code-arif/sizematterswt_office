<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Advertisement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'advertiseable_type',
        'advertiseable_id',
        'title',
        'subtitle',
        'image',
        'cta_label',
        'trigger_latitude',
        'trigger_longitude',
        'radius_meters',
        'status',
        'starts_at',
        'ends_at',
        'impression_count',
    ];

    protected $casts = [
        'trigger_latitude'  => 'float',
        'trigger_longitude' => 'float',
        'radius_meters'     => 'integer',
        'impression_count'  => 'integer',
        'starts_at'         => 'datetime',
        'ends_at'           => 'datetime',
    ];

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function advertiseable(): MorphTo
    {
        return $this->morphTo();
    }

    public function impressions(): HasMany
    {
        return $this->hasMany(AdImpression::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    // Helpers

    public function getLinkedTypeLabelAttribute(): string
    {
        if (! $this->advertiseable_type) return 'None';
        return class_basename($this->advertiseable_type);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function incrementImpressionCount(): void
    {
        $this->impression_count++;
        $this->save();
    }
}
