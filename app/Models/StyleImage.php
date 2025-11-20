<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StyleImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'path',
        'url',
        'filename',
        'original_filename',
        'file_size',
        'dimensions',
        'position',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'position' => 'integer',
    ];

    public function photoAnalysis()
    {
        return $this->belongsTo(Card::class);
    }
}

