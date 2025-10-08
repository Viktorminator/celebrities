<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        ProductCategory::create(['name' => 'Clothes', 'slug' => 'clothes']);
        ProductCategory::create(['name' => 'Perfume', 'slug' => 'perfume']);
        ProductCategory::create(['name' => 'Shoes', 'slug' => 'shoes']);
    }
}
