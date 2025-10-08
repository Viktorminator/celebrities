<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::create([
            'name' => 'Red Dress',
            'description' => 'A stunning red dress worn by Taylor Swift.',
            'price' => 19999,
            'image_url' => 'https://example.com/red-dress.jpg',
            'celebrity_id' => 1,
            'category_id' => 1,
            'occasion' => 'Award Show',
            'event_date' => '2023-05-01',
        ]);

        Product::create([
            'name' => 'Signature Perfume',
            'description' => 'David Beckham\'s signature fragrance.',
            'price' => 4999,
            'image_url' => 'https://example.com/beckham-perfume.jpg',
            'celebrity_id' => 2,
            'category_id' => 2,
            'occasion' => 'Launch Event',
            'event_date' => '2023-06-15',
        ]);
    }
}
