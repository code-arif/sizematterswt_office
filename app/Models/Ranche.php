<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Ranch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id',
        'name',
        'description',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'thumbnail',
        'tags',
        'acreage',
        'status',
        'is_featured',
        'marker_color',
        'marker_icon',
    ];

    protected $casts = [
        'tags'        => 'array',
        'is_featured' => 'boolean',
        'latitude'    => 'float',
        'longitude'   => 'float',
        'acreage'     => 'float',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(RanchMedia::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(RanchMedia::class)->where('media_type', 'image');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(RanchMedia::class)->where('media_type', 'video');
    }

    public function coverMedia(): HasMany
    {
        return $this->hasMany(RanchMedia::class)->where('is_cover', true);
    }

    public function visits(): MorphMany
    {
        return $this->morphMany(VisitedPlace::class, 'visitable');
    }

    public function events(): MorphMany
    {
        return $this->morphMany(Event::class, 'eventable');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNearby($query, float $lat, float $lng, float $km = 50)
    {
        return $query->selectRaw("
                *,
                ( 6371 * acos(
                    cos(radians(?)) * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?)) * sin(radians(latitude))
                )) AS distance", [$lat, $lng, $lat])
            ->having('distance', '<=', $km)
            ->orderBy('distance');
    }
}
