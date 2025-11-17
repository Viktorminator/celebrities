<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_link_id',
    ];

    /**
     * Get the user that favourited this link
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product link that was favourited
     */
    public function productLink()
    {
        return $this->belongsTo(ProductLink::class);
    }
}
