<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'description', 'price', 'image_url', 'celebrity_id', 'category_id', 'occasion', 'event_date'
    ];

    public function celebrity()
    {
        return $this->belongsTo(Celebrity::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
