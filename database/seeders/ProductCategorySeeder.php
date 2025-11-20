<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        Category::create(['name' => 'Clothes', 'slug' => 'clothes']);
        Category::create(['name' => 'Perfume', 'slug' => 'perfume']);
        Category::create(['name' => 'Shoes', 'slug' => 'shoes']);
    }
}
