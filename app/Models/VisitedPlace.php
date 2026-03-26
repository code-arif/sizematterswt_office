<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VisitedPlace extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'visitable_type',
        'visitable_id',
        'visited_at',
        'latitude',
        'longitude',
        'note',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'latitude'   => 'float',
        'longitude'  => 'float',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic parent — Farm, Ranch, or Event.
     * $visit->visitable  =>  Farm|Ranch|Event instance
     */
    public function visitable(): MorphTo
    {
        return $this->morphTo();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /** All visited farms for a user */
    public function scopeFarms($query)
    {
        return $query->where('visitable_type', Farm::class);
    }

    /** All visited ranches for a user */
    public function scopeRanches($query)
    {
        return $query->where('visitable_type', Ranche::class);
    }

    /** All visited events for a user */
    public function scopeEvents($query)
    {
        return $query->where('visitable_type', Event::class);
    }
}
