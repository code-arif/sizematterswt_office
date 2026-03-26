<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Farm extends Model
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
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /** Farm belongs to an Admin user */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /** All media (images + videos) for this farm */
    public function media(): HasMany
    {
        return $this->hasMany(FarmMedia::class);
    }

    /** Only images */
    public function images(): HasMany
    {
        return $this->hasMany(FarmMedia::class)->where('media_type', 'image');
    }

    /** Only videos */
    public function videos(): HasMany
    {
        return $this->hasMany(FarmMedia::class)->where('media_type', 'video');
    }

    /** Cover / featured media */
    public function coverMedia(): HasMany
    {
        return $this->hasMany(FarmMedia::class)->where('is_cover', true);
    }

    /** Users who have visited this farm */
    public function visits(): MorphMany
    {
        return $this->morphMany(VisitedPlace::class, 'visitable');
    }

    /** Users who have visited this farm */
    public function visitedPlaces(): HasMany
    {
        return $this->hasMany(VisitedPlace::class, 'visitable_id')
            ->where('visitable_type', self::class);
    }

    /** Events held at this farm */
    public function events(): MorphMany
    {
        return $this->morphMany(Event::class, 'eventable');
    }

    /**
     * Favourite farm
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'favoriteable_id')
            ->where('favoriteable_type', self::class);
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

    /**
     * Nearby farms within $km kilometres using Haversine formula.
     * Usage: Farm::nearby($lat, $lng, 50)->get();
     */
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
