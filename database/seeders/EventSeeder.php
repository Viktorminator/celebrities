<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;

class EventSeeder extends Seeder
{
    public function run()
    {
        Event::create([
            'title' => 'Grammy Awards',
            'date' => '2023-05-01',
            'description' => 'Taylor Swift at the Grammy Awards.',
            'image_url' => 'https://example.com/grammy.jpg',
            'category' => 'music',
            'celebrity_id' => 1,
        ]);

        Event::create([
            'title' => 'Perfume Launch',
            'date' => '2023-06-15',
            'description' => 'David Beckham launches his new perfume.',
            'image_url' => 'https://example.com/launch.jpg',
            'category' => 'fashion',
            'celebrity_id' => 2,
        ]);
    }
}
