<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Faker::create();

        echo "🚀 Starting comprehensive database seeding...\n";

        // Create storage directories
        $this->createStorageDirectories();

        // Create categories with images
        echo "📁 Creating categories...\n";
        $categories = $this->createCategories($faker);

        // Create 100+ posts with images
        echo "📝 Creating 100+ blog posts...\n";
        $this->createPosts($faker, $categories);

        echo "✅ Database seeding completed successfully!\n";
        echo "📊 Created:\n";
        echo '   - '.Category::count()." categories\n";
        echo '   - '.Post::count()." blog posts\n";
        echo "   - Random images for all content\n";
    }

    private function createStorageDirectories()
    {
        $directories = [
            'public/images',
            'public/images/posts',
            'public/images/categories',
            'public/images/generated',
        ];

        foreach ($directories as $dir) {
            if (! Storage::exists($dir)) {
                Storage::makeDirectory($dir);
            }
        }
    }

    private function createCategories($faker)
    {
        $categoryData = [
            [
                'en' => ['title' => 'Technology', 'description' => 'Latest technology news, reviews, and trends'],
                'lt' => ['title' => 'Technologijos', 'description' => 'Naujausios technologijų naujienos, apžvalgos ir tendencijos'],
            ],
            [
                'en' => ['title' => 'Design', 'description' => 'Creative design inspiration, tutorials, and showcases'],
                'lt' => ['title' => 'Dizainas', 'description' => 'Kūrybinio dizaino įkvėpimas, mokymai ir pristatymai'],
            ],
            [
                'en' => ['title' => 'Lifestyle', 'description' => 'Tips and insights for better living and wellness'],
                'lt' => ['title' => 'Gyvenimo būdas', 'description' => 'Patarimai ir įžvalgos geresniam gyvenimui ir sveikatai'],
            ],
            [
                'en' => ['title' => 'Business', 'description' => 'Entrepreneurship, marketing, and business strategies'],
                'lt' => ['title' => 'Verslas', 'description' => 'Verslininkystė, rinkodara ir verslo strategijos'],
            ],
            [
                'en' => ['title' => 'Travel', 'description' => 'Travel guides, destinations, and adventure stories'],
                'lt' => ['title' => 'Kelionės', 'description' => 'Kelionių vadovai, kelionių kryptys ir nuotykių istorijos'],
            ],
            [
                'en' => ['title' => 'Food & Cooking', 'description' => 'Recipes, cooking tips, and culinary adventures'],
                'lt' => ['title' => 'Maistas ir gaminimas', 'description' => 'Receptai, gaminimo patarimai ir kulinariniai nuotykiai'],
            ],
            [
                'en' => ['title' => 'Health & Fitness', 'description' => 'Workout routines, nutrition, and wellness advice'],
                'lt' => ['title' => 'Sveikata ir sportas', 'description' => 'Treniruočių programos, mityba ir sveikatos patarimai'],
            ],
            [
                'en' => ['title' => 'Photography', 'description' => 'Photography techniques, gear reviews, and inspiration'],
                'lt' => ['title' => 'Fotografija', 'description' => 'Fotografavimo metodai, įrangos apžvalgos ir įkvėpimas'],
            ],
        ];

        $categories = [];

        foreach ($categoryData as $index => $data) {
            $category = Category::create([
                'published' => true,
                'position' => $index + 1,
            ]);

            // Create translations
            foreach (['en', 'lt'] as $locale) {
                $category->translations()->create([
                    'locale' => $locale,
                    'active' => true,
                    'title' => $data[$locale]['title'],
                    'description' => $data[$locale]['description'],
                ]);
            }

            // Generate fake image for category
            $this->generateCategoryImage($category, $data['en']['title']);

            $categories[] = $category;
        }

        return $categories;
    }

    private function createPosts($faker, $categories)
    {
        $postCount = 120; // Create 120 posts for good distribution

        for ($i = 1; $i <= $postCount; $i++) {
            echo "Creating post {$i}/{$postCount}...\r";

            $isPublished = $faker->boolean(85); // 85% published
            $publishedAt = $isPublished ? $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s') : null;
            $post = Post::create([
                'published' => $isPublished,
                'published_at' => $publishedAt,
                'position' => $i,
            ]);

            // Generate English content
            $enTitle = $faker->sentence(rand(3, 8));
            $enDescription = $faker->paragraph(rand(2, 4));
            $enContent = $this->generateRichContent($faker);

            $post->translations()->create([
                'locale' => 'en',
                'active' => true,
                'title' => $enTitle,
                'description' => $enDescription,
                'content' => $enContent,
            ]);

            // Generate Lithuanian content
            $ltTitle = $this->translateToLithuanian($enTitle, $faker);
            $ltDescription = $this->translateToLithuanian($enDescription, $faker);
            $ltContent = $this->generateRichContentLT($faker);

            $post->translations()->create([
                'locale' => 'lt',
                'active' => true,
                'title' => $ltTitle,
                'description' => $ltDescription,
                'content' => $ltContent,
            ]);

            // Assign random categories (1-3 categories per post)
            $postCategories = $faker->randomElements($categories, rand(1, 3));
            $categoryIds = array_map(fn ($cat) => $cat->id, $postCategories);
            $post->categories()->attach($categoryIds);

            // Generate fake image for post
            $this->generatePostImage($post, $enTitle);

            // Random created_at for realistic distribution
            $post->update([
                'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                'updated_at' => $faker->dateTimeBetween($post->created_at, 'now'),
            ]);
        }

        echo "\n";
    }

    private function generateRichContent($faker)
    {
        $paragraphs = [];
        $numParagraphs = rand(5, 12);

        for ($i = 0; $i < $numParagraphs; $i++) {
            $paragraphs[] = '<p>'.$faker->paragraph(rand(4, 8)).'</p>';

            // Occasionally add headings, lists, or quotes
            if ($i > 0 && rand(1, 4) === 1) {
                switch (rand(1, 3)) {
                    case 1:
                        $paragraphs[] = '<h3>'.$faker->sentence(rand(3, 6)).'</h3>';
                        break;
                    case 2:
                        $listItems = [];
                        for ($j = 0; $j < rand(3, 6); $j++) {
                            $listItems[] = '<li>'.$faker->sentence(rand(2, 5)).'</li>';
                        }
                        $paragraphs[] = '<ul>'.implode('', $listItems).'</ul>';
                        break;
                    case 3:
                        $paragraphs[] = '<blockquote><p>'.$faker->paragraph(rand(2, 4)).'</p></blockquote>';
                        break;
                }
            }
        }

        return implode('', $paragraphs);
    }

    private function generateRichContentLT($faker)
    {
        $ltPhrases = [
            'Šiuolaikiniame pasaulyje',
            'Svarbu paminėti, kad',
            'Tyrimo duomenys rodo',
            'Ekspertai teigia',
            'Praktikoje tai reiškia',
            'Kita vertus',
            'Nepaisant to',
            'Reikia atkreipti dėmesį',
            'Pagal specialistus',
            'Šis metodas leidžia',
        ];

        $paragraphs = [];
        $numParagraphs = rand(5, 12);

        for ($i = 0; $i < $numParagraphs; $i++) {
            $content = $faker->randomElement($ltPhrases).' '.$faker->paragraph(rand(4, 8));
            $paragraphs[] = '<p>'.$content.'</p>';

            if ($i > 0 && rand(1, 4) === 1) {
                switch (rand(1, 3)) {
                    case 1:
                        $paragraphs[] = '<h3>'.$faker->sentence(rand(3, 6)).'</h3>';
                        break;
                    case 2:
                        $listItems = [];
                        for ($j = 0; $j < rand(3, 6); $j++) {
                            $listItems[] = '<li>'.$faker->sentence(rand(2, 5)).'</li>';
                        }
                        $paragraphs[] = '<ul>'.implode('', $listItems).'</ul>';
                        break;
                    case 3:
                        $paragraphs[] = '<blockquote><p>'.$faker->paragraph(rand(2, 4)).'</p></blockquote>';
                        break;
                }
            }
        }

        return implode('', $paragraphs);
    }

    private function translateToLithuanian($text, $faker)
    {
        // Simple translation mapping for common words
        $translations = [
            'Technology' => 'Technologijos',
            'Design' => 'Dizainas',
            'Business' => 'Verslas',
            'Travel' => 'Kelionės',
            'Food' => 'Maistas',
            'Health' => 'Sveikata',
            'Photography' => 'Fotografija',
            'The' => '',
            'and' => 'ir',
            'or' => 'arba',
            'with' => 'su',
            'for' => 'dėl',
            'in' => 'į',
            'on' => 'ant',
            'at' => 'ties',
        ];

        $translated = $text;
        foreach ($translations as $en => $lt) {
            $translated = str_ireplace($en, $lt, $translated);
        }

        return trim($translated);
    }

    private function generatePostImage($post, $title)
    {
        try {
            // Create a simple colored rectangle with text
            $width = 800;
            $height = 400;

            $image = Image::canvas($width, $height, $this->getRandomColor());

            // Add some geometric shapes for visual interest
            $this->addRandomShapes($image, $width, $height);

            // Add title text
            $shortTitle = strlen($title) > 50 ? substr($title, 0, 47).'...' : $title;

            $filename = 'post_'.$post->id.'_'.time().'.jpg';
            $path = storage_path('app/public/images/posts/'.$filename);

            $image->save($path, 80);

            return 'images/posts/'.$filename;
        } catch (\Exception $e) {
            // Fallback: return a placeholder path
            return 'images/placeholder.jpg';
        }
    }

    private function generateCategoryImage($category, $title)
    {
        try {
            $width = 600;
            $height = 300;

            $image = Image::canvas($width, $height, $this->getRandomColor());

            // Add category-specific patterns
            $this->addCategoryPattern($image, $width, $height, $title);

            $filename = 'category_'.$category->id.'_'.time().'.jpg';
            $path = storage_path('app/public/images/categories/'.$filename);

            $image->save($path, 80);

            return 'images/categories/'.$filename;
        } catch (\Exception $e) {
            return 'images/placeholder.jpg';
        }
    }

    private function getRandomColor()
    {
        $colors = [
            '#3B82F6', '#EF4444', '#10B981', '#F59E0B',
            '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16',
            '#F97316', '#6366F1', '#14B8A6', '#F43F5E',
        ];

        return $colors[array_rand($colors)];
    }

    private function addRandomShapes($image, $width, $height)
    {
        $numShapes = rand(3, 8);

        for ($i = 0; $i < $numShapes; $i++) {
            $shape = rand(1, 3);
            $color = $this->getRandomColor();

            switch ($shape) {
                case 1: // Circle
                    $x = rand(0, $width);
                    $y = rand(0, $height);
                    $radius = rand(20, 100);
                    $image->circle($radius, $x, $y, function ($draw) use ($color) {
                        $draw->background($color);
                    });
                    break;

                case 2: // Rectangle
                    $x1 = rand(0, $width - 100);
                    $y1 = rand(0, $height - 100);
                    $x2 = $x1 + rand(50, 200);
                    $y2 = $y1 + rand(30, 150);
                    $image->rectangle($x1, $y1, $x2, $y2, function ($draw) use ($color) {
                        $draw->background($color);
                    });
                    break;

                case 3: // Line
                    $x1 = rand(0, $width);
                    $y1 = rand(0, $height);
                    $x2 = rand(0, $width);
                    $y2 = rand(0, $height);
                    $image->line($x1, $y1, $x2, $y2, function ($draw) use ($color) {
                        $draw->color($color);
                        $draw->width(rand(2, 8));
                    });
                    break;
            }
        }
    }

    private function addCategoryPattern($image, $width, $height, $category)
    {
        // Add category-specific visual elements
        switch (strtolower($category)) {
            case 'technology':
                $this->addTechPattern($image, $width, $height);
                break;
            case 'design':
                $this->addDesignPattern($image, $width, $height);
                break;
            case 'travel':
                $this->addTravelPattern($image, $width, $height);
                break;
            default:
                $this->addRandomShapes($image, $width, $height);
                break;
        }
    }

    private function addTechPattern($image, $width, $height)
    {
        // Add circuit-like patterns
        for ($i = 0; $i < 10; $i++) {
            $x1 = rand(0, $width);
            $y1 = rand(0, $height);
            $x2 = $x1 + rand(-100, 100);
            $y2 = $y1 + rand(-100, 100);

            $image->line($x1, $y1, $x2, $y2, function ($draw) {
                $draw->color('#00FF00');
                $draw->width(2);
            });
        }
    }

    private function addDesignPattern($image, $width, $height)
    {
        // Add artistic circles and gradients
        for ($i = 0; $i < 5; $i++) {
            $x = rand(0, $width);
            $y = rand(0, $height);
            $radius = rand(30, 120);

            $image->circle($radius, $x, $y, function ($draw) {
                $draw->background('rgba(255, 255, 255, 0.3)');
            });
        }
    }

    private function addTravelPattern($image, $width, $height)
    {
        // Add mountain-like triangular shapes
        for ($i = 0; $i < 6; $i++) {
            $points = [
                rand(0, $width), rand($height / 2, $height),  // bottom left
                rand(0, $width), rand(0, $height / 2),        // top
                rand(0, $width), rand($height / 2, $height),   // bottom right
            ];

            $image->polygon($points, function ($draw) {
                $draw->background('rgba(255, 255, 255, 0.2)');
            });
        }
    }
}
