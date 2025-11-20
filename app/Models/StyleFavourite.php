<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StyleFavourite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_id',
        'session_id',
    ];

    /**
     * Get the user that favourited this style
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the photo analysis (style) that was favourited
     */
    public function photoAnalysis()
    {
        return $this->belongsTo(Card::class);
    }
}
