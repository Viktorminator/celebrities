<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetectedItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'category',
        'description',
        'color',
        'confidence',
        'bounding_box',
        'raw_data'
    ];

    protected $casts = [
        'confidence' => 'decimal:2',
        'bounding_box' => 'array',
        'raw_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the photo analysis that owns this detected item
     */
    public function photoAnalysis()
    {
        return $this->belongsTo(Card::class);
    }

    /**
     * Get the product links for this detected item
     */
    public function productLinks()
    {
        return $this->hasMany(ProductLink::class);
    }

    /**
     * Check if item has high confidence (>= 80%)
     */
    public function hasHighConfidence()
    {
        return $this->confidence >= 80;
    }

    /**
     * Check if item has medium confidence (50-79%)
     */
    public function hasMediumConfidence()
    {
        return $this->confidence >= 50 && $this->confidence < 80;
    }

    /**
     * Check if item has low confidence (< 50%)
     */
    public function hasLowConfidence()
    {
        return $this->confidence < 50;
    }

    /**
     * Get confidence level as string
     */
    public function getConfidenceLevelAttribute()
    {
        if ($this->hasHighConfidence()) {
            return 'high';
        } elseif ($this->hasMediumConfidence()) {
            return 'medium';
        }
        return 'low';
    }

    /**
     * Get formatted description with color
     */
    public function getFullDescriptionAttribute()
    {
        if ($this->color) {
            return ucfirst($this->color) . ' ' . $this->description;
        }
        return $this->description;
    }

    /**
     * Get search query for products
     */
    public function getSearchQueryAttribute()
    {
        $query = $this->description;

        if ($this->color) {
            $query = $this->color . ' ' . $query;
        }

        return $query;
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by minimum confidence
     */
    public function scopeMinConfidence($query, $confidence = 50)
    {
        return $query->where('confidence', '>=', $confidence);
    }

    /**
     * Scope to get items with bounding boxes
     */
    public function scopeWithBoundingBox($query)
    {
        return $query->whereNotNull('bounding_box');
    }
}
