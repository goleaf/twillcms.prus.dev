<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology', 'description' => 'Latest technology news and updates'],
            ['name' => 'Business', 'slug' => 'business', 'description' => 'Business insights and trends'],
            ['name' => 'Lifestyle', 'slug' => 'lifestyle', 'description' => 'Lifestyle tips and inspiration'],
            ['name' => 'Health', 'slug' => 'health', 'description' => 'Health and wellness articles'],
            ['name' => 'Travel', 'slug' => 'travel', 'description' => 'Travel guides and destinations'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
