<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    }
}
<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();

        $posts = [
            [
                'title' => 'Welcome to Our CMS',
                'content' => 'This is a sample post to demonstrate the CMS functionality. You can edit, delete, or create new posts from the admin panel.',
                'excerpt' => 'Welcome post for the CMS demonstration',
                'status' => 'published',
                'published_at' => now(),
            ],
            [
                'title' => 'Getting Started with Laravel',
                'content' => 'Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling.',
                'excerpt' => 'Introduction to Laravel framework',
                'status' => 'published',
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'Building Modern Web Applications',
                'content' => 'Modern web applications require robust architecture, clean code, and efficient database design. This post covers best practices for building scalable applications.',
                'excerpt' => 'Best practices for modern web development',
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Draft Post Example',
                'content' => 'This is a draft post that is not yet published. It can be edited and published later.',
                'excerpt' => 'Example of a draft post',
                'status' => 'draft',
                'published_at' => null,
            ],
        ];

        foreach ($posts as $postData) {
            $post = Post::create([
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'content' => $postData['content'],
                'excerpt' => $postData['excerpt'],
                'status' => $postData['status'],
                'published_at' => $postData['published_at'],
                'user_id' => $users->random()->id,
            ]);

            // Attach random categories
            $post->categories()->attach($categories->random(rand(1, 3)));
        }
    }
}