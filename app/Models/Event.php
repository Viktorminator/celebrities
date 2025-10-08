<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'date', 'description', 'image_url', 'category', 'celebrity_id'
    ];

    public function celebrity()
    {
        return $this->belongsTo(Celebrity::class);
    }
}
