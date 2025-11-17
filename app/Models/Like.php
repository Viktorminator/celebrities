<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photo_analysis_id',
    ];

    /**
     * Get the user that liked this style
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the photo analysis (style) that was liked
     */
    public function photoAnalysis()
    {
        return $this->belongsTo(PhotoAnalysis::class);
    }
}
