<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'subscription_plan',
        'subscription_ends_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_ends_at' => 'datetime',
        ];
    }

    /**
     * Get the photo analyses for the user
     */
    public function photoAnalyses()
    {
        return $this->hasMany(PhotoAnalysis::class);
    }

    /**
     * Get the product links for the user
     */
    public function productLinks()
    {
        return $this->hasMany(ProductLink::class);
    }

    /**
     * Get the style limit based on subscription plan
     */
    public function styleLimit(): ?int
    {
        $plan = $this->subscription_plan ?? 'free';

        $limits = [
            'free' => 10,
            'pro' => 100,
            'premium' => null, // Unlimited
        ];

        return $limits[$plan] ?? 10;
    }

    /**
     * Determine if user has reached their style limit
     */
    public function hasReachedStyleLimit(): bool
    {
        $limit = $this->styleLimit();

        if ($limit === null) {
            return false;
        }

        $currentCount = $this->photoAnalyses()
            ->where('status', '!=', 'failed')
            ->count();

        return $currentCount >= $limit;
    }

    /**
     * Get the favourites for the user
     */
    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    /**
     * Get the likes for the user
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get the style favourites for the user
     */
    public function styleFavourites()
    {
        return $this->hasMany(StyleFavourite::class);
    }

    /**
     * Check if user has subscribed (has active subscription)
     */
    public function hasActiveSubscription(): bool
    {
        if (!$this->subscription_plan || $this->subscription_plan === 'free') {
            return false;
        }

        if ($this->subscription_ends_at && $this->subscription_ends_at->isPast()) {
            return false;
        }

        return true;
    }
}
