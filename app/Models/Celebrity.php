<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Celebrity extends Model
{
    protected $casts = [
        'categories' => 'array',
    ];

    protected $fillable = [
        'name', 'profession', 'bio', 'image_url', 'banner_url', 'categories', 'likes'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
