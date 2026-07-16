<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'email',
        'phone',
        'password',
        'status',
        'email_verified_at',
        'provider',
        'provider_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /*
    |----------------------------------------------------------------------
    | JWT Interface Methods
    |----------------------------------------------------------------------
    */

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'role'   => $this->getRoleNames()->first(),
            'status' => $this->status,
        ];
    }

    // -------------------------------------------------------------------------
    // Role helpers
    // -------------------------------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function farms(): HasMany
    {
        return $this->hasMany(Farm::class, 'admin_id');
    }

    public function ranches(): HasMany
    {
        return $this->hasMany(Ranche::class, 'admin_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'admin_id');
    }

    // public function ads(): HasMany
    // {
    //     return $this->hasMany(Ad::class, 'admin_id');
    // }


     // -------------------------------------------------------------------------
    // User relationships (things a regular user does)
    // -------------------------------------------------------------------------

    /** All visited places (farms, ranches, events) */
    public function visitedPlaces(): HasMany
    {
        return $this->hasMany(VisitedPlace::class);
    }

    /** User notes */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /** User subscription */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    /** Check if user has an active subscription */
    public function hasActiveSubscription(): bool
    {
        $subscription = $this->subscription;
        if (!$subscription) {
            return false;
        }

        return $subscription->status === 'active' && 
               ($subscription->expires_at === null || $subscription->expires_at->isFuture());
    }
}
