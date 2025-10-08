<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Celebrity;

class CelebritySeeder extends Seeder
{
    public function run()
    {
        Celebrity::create([
            'name' => 'Taylor Swift',
            'profession' => 'Singer',
            'bio' => 'American singer-songwriter.',
            'image_url' => 'https://example.com/taylor.jpg',
            'banner_url' => 'https://example.com/taylor-banner.jpg',
            'categories' => ['music', 'fashion'],
            'likes' => 1000,
        ]);

        Celebrity::create([
            'name' => 'David Beckham',
            'profession' => 'Footballer',
            'bio' => 'English former professional footballer.',
            'image_url' => 'https://example.com/beckham.jpg',
            'banner_url' => 'https://example.com/beckham-banner.jpg',
            'categories' => ['sports', 'fashion'],
            'likes' => 800,
        ]);
    }
}
