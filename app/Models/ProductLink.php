<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'detected_item_id',
        'platform',
        'title',
        'url',
        'price',
        'image_url',
        'asin',
        'search_query',
        'raw_data'
    ];

    protected $casts = [
        'raw_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the detected item that owns this product link
     */
    public function detectedItem()
    {
        return $this->belongsTo(DetectedItem::class);
    }

    /**
     * Get photo analysis through detected item
     */
    public function photoAnalysis()
    {
        return $this->hasOneThrough(
            PhotoAnalysis::class,
            DetectedItem::class,
            'id',
            'id',
            'detected_item_id',
            'photo_analysis_id'
        );
    }

    /**
     * Check if this is an Amazon link
     */
    public function isAmazon()
    {
        return $this->platform === 'Amazon';
    }

    /**
     * Check if this is a Google Shopping link
     */
    public function isGoogleShopping()
    {
        return $this->platform === 'Google Shopping';
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        if (!$this->price || $this->price === 'N/A') {
            return 'Price not available';
        }

        // If price already has currency symbol, return as is
        if (preg_match('/[$€£¥]/', $this->price)) {
            return $this->price;
        }

        // Otherwise add dollar sign
        return '$' . $this->price;
    }

    /**
     * Check if product has image
     */
    public function hasImage()
    {
        return !empty($this->image_url);
    }

    /**
     * Check if product has price
     */
    public function hasPrice()
    {
        return !empty($this->price) && $this->price !== 'N/A';
    }

    /**
     * Get Amazon product page URL (if ASIN available)
     */
    public function getAmazonProductUrlAttribute()
    {
        if ($this->asin && $this->asin !== 'N/A') {
            $tag = config('services.amazon.associate_tag');
            $marketplace = config('services.amazon.marketplace', 'www.amazon.com');
            return "https://{$marketplace}/dp/{$this->asin}?tag={$tag}";
        }
        return $this->url;
    }

    /**
     * Scope to filter by platform
     */
    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to get only Amazon products
     */
    public function scopeAmazon($query)
    {
        return $query->where('platform', 'Amazon');
    }

    /**
     * Scope to get products with images
     */
    public function scopeWithImages($query)
    {
        return $query->whereNotNull('image_url');
    }

    /**
     * Scope to get products with prices
     */
    public function scopeWithPrices($query)
    {
        return $query->whereNotNull('price')
            ->where('price', '!=', 'N/A');
    }
}
