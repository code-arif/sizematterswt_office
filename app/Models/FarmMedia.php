<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FarmMedia extends Model
{
    use HasFactory;

    protected $table = 'farm_media';

    protected $fillable = [
        'farm_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'media_type',
        'thumbnail_path',
        'duration_seconds',
        'caption',
        'is_cover',
        'sort_order',
    ];

    protected $casts = [
        'is_cover'         => 'boolean',
        'file_size'        => 'integer',
        'duration_seconds' => 'integer',
        'sort_order'       => 'integer',
    ];

    protected $appends = ['url', 'thumbnail_url'];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /** Full public URL of the file */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /** Full public URL of the video thumbnail (null for images) */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path
            ? Storage::url($this->thumbnail_path)
            : null;
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------
    public function scopeImages($query)
    {
        return $query->where('media_type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('media_type', 'video');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
