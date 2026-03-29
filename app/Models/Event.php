<?php

namespace App\Models;

use App\Models\Favorite;
use App\Models\VisitedPlace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id',
        'title',
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
        'image',
        'start_date',
        'end_date',
        'entry_fee',
        'capacity',
        'eventable_type',
        'eventable_id',
        'status',
        'owner_name',
        'owner_address',
        'owner_phone',
        'owner_avatar',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'entry_fee'  => 'float',
        'capacity'   => 'integer',
        'latitude'   => 'float',
        'longitude'  => 'float',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Polymorphic parent — could be a Farm or a Ranch.
     * $event->eventable  =>  Farm|Ranch instance
     */
    public function eventable(): MorphTo
    {
        return $this->morphTo();
    }

    /** Users who have visited this event */
    public function visits(): MorphMany
    {
        return $this->morphMany(VisitedPlace::class, 'visitable');
    }

    /** All media files attached to this event */
    // public function media(): HasMany
    // {
    //     return $this->hasMany(EventMedia::class);
    // }

    /** Image-only media */
    public function images(): HasMany
    {
        return $this->hasMany(EventMedia::class)->where('media_type', 'image');
    }

    /** Video-only media */
    public function videos(): HasMany
    {
        return $this->hasMany(EventMedia::class)->where('media_type', 'video');
    }

    /** Cover media files */
    public function coverMedia(): HasMany
    {
        return $this->hasMany(EventMedia::class)->where('is_cover', true);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')->where('start_date', '>=', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
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

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'favoriteable_id')
            ->where('favoriteable_type', self::class);
    }

    public function visitedPlaces(): HasMany
    {
        return $this->hasMany(VisitedPlace::class, 'visitable_id')
            ->where('visitable_type', self::class);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isFree(): bool
    {
        return is_null($this->entry_fee) || $this->entry_fee == 0;
    }

    // Relationship section-e giye eivabe likhun:
    public function media(): HasMany
    {

        return $this->hasMany(EventMedia::class, 'events_id');
    }
}
