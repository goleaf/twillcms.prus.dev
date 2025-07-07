<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'Laravel', 'slug' => 'laravel'],
            ['name' => 'PHP', 'slug' => 'php'],
            ['name' => 'Web Development', 'slug' => 'web-development'],
            ['name' => 'JavaScript', 'slug' => 'javascript'],
            ['name' => 'Vue.js', 'slug' => 'vuejs'],
            ['name' => 'Database', 'slug' => 'database'],
            ['name' => 'API', 'slug' => 'api'],
            ['name' => 'Frontend', 'slug' => 'frontend'],
            ['name' => 'Backend', 'slug' => 'backend'],
            ['name' => 'Tutorial', 'slug' => 'tutorial'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
