<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'image_url',
        'file_size',
        'dimensions',
        'analysis_metadata',
        'status',
        'detected_celebrities',
        'face_count',
        'has_person',
        'context_labels'
    ];

    protected $casts = [
        'analysis_metadata' => 'array',
        'detected_celebrities' => 'array',
        'context_labels' => 'array',
        'has_person' => 'boolean',
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

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
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
}
