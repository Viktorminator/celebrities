<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this analysis
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the detected items for this analysis
     */
    public function detectedItems()
    {
        return $this->hasMany(DetectedItem::class);
    }

    /**
     * Check if analysis is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if analysis is processing
     */
    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    /**
     * Check if analysis failed
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Get all product links through detected items
     */
    public function productLinks()
    {
        return $this->hasManyThrough(ProductLink::class, DetectedItem::class);
    }

    /**
     * Mark analysis as completed
     */
    public function markCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Mark analysis as failed
     */
    public function markFailed()
    {
        $this->update(['status' => 'failed']);
    }

    public function images()
    {
        return $this->hasMany(StyleImage::class)->orderBy('position');
    }

    /**
     * Get all images for this style (for multi-image support)
     */
    public function getAllImages()
    {
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();

        if ($images->isNotEmpty()) {
            return $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'path' => $image->path,
                    'url' => $image->url,
                    'filename' => $image->filename,
                    'original_filename' => $image->original_filename,
                    'file_size' => $image->file_size,
                    'dimensions' => $image->dimensions,
                ];
            })->toArray();
        }

        $metadata = $this->analysis_metadata ?? [];

        return [[
            'id' => null,
            'path' => $this->image_path,
            'url' => $this->image_url,
            'filename' => basename($this->image_path ?? ''),
            'original_filename' => $metadata['original_filename'] ?? null,
            'file_size' => $this->file_size,
            'dimensions' => $this->dimensions,
        ]];
    }

    /**
     * Get image count
     */
    public function getImageCount()
    {
        $images = $this->getAllImages();
        return count($images);
    }

    /**
     * Scope to get only completed analyses
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get recent analyses
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get the likes for this style
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Check if a user has liked this style
     */
    public function isLikedBy($userId)
    {
        if (!$userId) {
            return false;
        }
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Get the count of likes
     */
    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    /**
     * Get the style favourites for this style
     */
    public function styleFavourites()
    {
        return $this->hasMany(StyleFavourite::class, 'card_id');
    }

    /**
     * Check if a user/session has favourited this style
     */
    public function isFavouritedBy($userId = null, $sessionId = null)
    {
        if ($userId) {
            return $this->styleFavourites()->where('user_id', $userId)->exists();
        }
        if ($sessionId) {
            return $this->styleFavourites()->where('session_id', $sessionId)->exists();
        }
        return false;
    }

    /**
     * Get the count of style favourites
     */
    public function getStyleFavouritesCountAttribute()
    {
        return $this->styleFavourites()->count();
    }

    /**
     * Get the style tags for this analysis
     */
    public function styleTags()
    {
        return $this->hasMany(StyleTag::class);
    }

    /**
     * Get tags as array (for backward compatibility)
     */
    public function getTagsAttribute()
    {
        return $this->styleTags->pluck('tag')->toArray();
    }
}
