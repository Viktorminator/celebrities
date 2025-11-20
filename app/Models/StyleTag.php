<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StyleTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'tag',
    ];

    /**
     * Get the photo analysis that owns this tag
     */
    public function photoAnalysis()
    {
        return $this->belongsTo(Card::class);
    }
}
