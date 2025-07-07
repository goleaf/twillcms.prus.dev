<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting database seeding...');

        // Create admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Create test user
        $testUser = User::firstOrCreate([
            'email' => 'test@example.com'
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Users created');

        // Create categories
        $categoriesData = [
            [
                'title' => 'Technology',
                'slug' => 'technology',
                'description' => 'Latest technology news, innovations, and digital trends',
                'position' => 1,
            ],
            [
                'title' => 'Business',
                'slug' => 'business',
                'description' => 'Business news, market analysis, and entrepreneurship',
                'position' => 2,
            ],
            [
                'title' => 'Politics',
                'slug' => 'politics',
                'description' => 'Political news and government updates',
                'position' => 3,
            ],
            [
                'title' => 'Sports',
                'slug' => 'sports',
                'description' => 'Sports news, scores, and athlete stories',
                'position' => 4,
            ],
            [
                'title' => 'Health',
                'slug' => 'health',
                'description' => 'Health and wellness news and advice',
                'position' => 5,
            ],
            [
                'title' => 'Science',
                'slug' => 'science',
                'description' => 'Scientific discoveries and research breakthroughs',
                'position' => 6,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $categoryData) {
            $category = Category::firstOrCreate([
                'slug' => $categoryData['slug']
            ], array_merge($categoryData, [
                'published' => true,
            ]));
            $categories[] = $category;
        }

        $this->command->info('✅ Categories created: ' . count($categories));

        // Create posts
        $postsData = [
            [
                'title' => 'The Future of Artificial Intelligence in 2025',
                'content' => "<p>Artificial Intelligence continues to reshape our world in unprecedented ways. As we move into 2025, AI technologies are becoming more sophisticated and accessible than ever before.</p><p>Machine learning algorithms are now capable of processing vast amounts of data in real-time, enabling breakthrough applications in healthcare, finance, and transportation. Recent developments in natural language processing have made AI assistants more conversational and helpful.</p><h3>Key AI Developments</h3><ul><li>Advanced neural networks capable of creative tasks</li><li>Improved computer vision for autonomous vehicles</li><li>AI-powered drug discovery accelerating medical research</li><li>Enhanced cybersecurity through predictive threat analysis</li></ul><p>The integration of AI into everyday life is accelerating, with smart homes, personalized education, and AI-driven content creation becoming mainstream. However, this rapid advancement also brings important considerations about ethics, privacy, and the future of work.</p><p>As we navigate this AI revolution, it's crucial to balance innovation with responsible development, ensuring that these powerful technologies benefit humanity as a whole.</p>",
                'description' => 'Exploring the latest developments in AI technology and their impact on society, business, and daily life in 2025.',
                'excerpt' => 'AI continues to transform industries with breakthrough applications in healthcare, finance, and beyond.',
                'categories' => ['technology', 'science'],
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'Global Economic Outlook: Market Trends to Watch',
                'content' => "<p>The global economy is experiencing significant shifts as we progress through 2025. Understanding current market trends is essential for businesses and investors navigating these uncertain times.</p><p>Inflation rates have shown signs of stabilization in major economies, while supply chain disruptions continue to affect various sectors. Central banks worldwide are adjusting monetary policies to balance growth with price stability.</p><h3>Key Economic Indicators</h3><ul><li>GDP growth rates across major economies</li><li>Employment figures and labor market dynamics</li><li>Currency fluctuations and international trade patterns</li><li>Energy prices and their ripple effects</li></ul><p>Emerging markets are displaying resilience, with several developing nations outpacing traditional economic powerhouses. The technology sector remains a driving force, while renewable energy investments are reshaping the energy landscape.</p><p>Sustainable business practices are no longer optional but essential for long-term success. Companies that adapt to environmental and social governance (ESG) criteria are better positioned for future growth.</p>",
                'description' => 'Analysis of current global economic trends, market indicators, and business implications for 2025.',
                'excerpt' => 'Navigate the changing economic landscape with insights into market trends and business opportunities.',
                'categories' => ['business'],
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Climate Change Action: Government Policies and Initiatives',
                'content' => "<p>Governments worldwide are implementing comprehensive policies to address climate change, marking a pivotal moment in environmental legislation and international cooperation.</p><p>The latest climate summit has resulted in ambitious commitments from nations across the globe. New carbon reduction targets, renewable energy investments, and sustainable development goals are being established at an unprecedented pace.</p><h3>Major Policy Developments</h3><ul><li>Carbon pricing mechanisms in developing nations</li><li>Massive investments in renewable energy infrastructure</li><li>Stricter emissions standards for transportation</li><li>International cooperation on climate technology transfer</li></ul><p>Public-private partnerships are emerging as a crucial mechanism for implementing large-scale environmental projects. Cities are leading by example with smart urban planning that prioritizes sustainability and resilience.</p><p>The transition to clean energy is creating new economic opportunities while challenging traditional industries to adapt. Green jobs are becoming a significant part of the employment landscape.</p>",
                'description' => 'Overview of government climate policies, international cooperation, and the path toward sustainable development.',
                'excerpt' => 'Explore how governments are tackling climate change through innovative policies and international collaboration.',
                'categories' => ['politics', 'science'],
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Sports Revolution: Technology Transforming Athletics',
                'content' => "<p>The world of sports is experiencing a technological revolution that's changing how athletes train, compete, and recover. From advanced analytics to cutting-edge equipment, innovation is reshaping athletic performance.</p><p>Wearable technology has become an integral part of professional sports, providing real-time data on athlete performance, health metrics, and injury prevention. Teams are using this information to optimize training regimens and game strategies.</p><h3>Breakthroughs in Sports Technology</h3><ul><li>Biomechanical analysis for performance optimization</li><li>AI-powered coaching and strategy development</li><li>Advanced materials in equipment design</li><li>Virtual reality training simulations</li></ul><p>Fan engagement has also evolved with immersive viewing experiences, interactive broadcasts, and social media integration. Stadiums are becoming smart venues with enhanced connectivity and personalized services.</p><p>Youth sports programs are incorporating technology to make training safer and more effective, helping develop the next generation of athletes with better tools and techniques.</p>",
                'description' => 'How technology is revolutionizing sports performance, fan engagement, and the future of athletics.',
                'excerpt' => 'Discover how cutting-edge technology is transforming sports from training to fan experience.',
                'categories' => ['sports', 'technology'],
                'published_at' => now()->subDays(4),
            ],
            [
                'title' => 'Mental Health Awareness: Breaking the Stigma',
                'content' => "<p>Mental health awareness has reached a turning point, with society increasingly recognizing the importance of psychological well-being and breaking down long-standing stigmas.</p><p>Recent surveys show that more people are seeking mental health support, and healthcare systems are expanding access to psychological services. Workplace mental health programs are becoming standard practice in progressive organizations.</p><h3>Key Developments in Mental Health Support</h3><ul><li>Increased funding for mental health services</li><li>Integration of mental health into primary healthcare</li><li>Workplace wellness programs and stress management</li><li>Technology-assisted therapy and counseling</li></ul><p>Social media platforms are taking responsibility for their impact on mental health, implementing features to promote positive interactions and reduce harmful content. Mental health education is being integrated into school curricula from an early age.</p><p>The COVID-19 pandemic has highlighted the crucial connection between physical and mental health, leading to more holistic approaches to healthcare.</p>",
                'description' => 'Examining progress in mental health awareness, treatment accessibility, and societal attitude changes.',
                'excerpt' => 'Mental health awareness is growing as society works to eliminate stigma and improve access to care.',
                'categories' => ['health'],
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Space Exploration: Mars Mission Updates and Beyond',
                'content' => "<p>Space exploration is entering a new golden age with ambitious missions to Mars, innovative spacecraft technologies, and international collaboration reaching unprecedented levels.</p><p>The latest Mars missions have provided groundbreaking insights into the Red Planet's geology, atmosphere, and potential for past or present life. Advanced rovers and orbiters are collecting data that will inform future human missions.</p><h3>Major Space Exploration Milestones</h3><ul><li>Successful sample return missions from asteroids</li><li>Advanced propulsion systems for deep space travel</li><li>International cooperation on lunar base development</li><li>Private sector contributions to space technology</li></ul><p>Commercial space companies are revolutionizing access to space with reusable rockets and cost-effective launch systems. This democratization of space access is enabling new scientific missions and commercial opportunities.</p><p>The search for extraterrestrial life continues with sophisticated instruments scanning the cosmos for signs of biological activity. Recent discoveries of exoplanets in habitable zones fuel optimism about finding life beyond Earth.</p>",
                'description' => 'Latest developments in space exploration, Mars missions, and the search for extraterrestrial life.',
                'excerpt' => 'Space exploration reaches new heights with Mars missions and breakthrough discoveries in our solar system.',
                'categories' => ['science', 'technology'],
                'published_at' => now()->subDays(6),
            ],
            [
                'title' => 'Cybersecurity in the Digital Age: Protecting Your Data',
                'content' => "<p>Cybersecurity has become a critical concern as our lives become increasingly digital. Understanding threats and protection strategies is essential for individuals and organizations alike.</p><p>Cyber attacks are becoming more sophisticated, targeting everything from personal devices to critical infrastructure. Recent incidents have highlighted vulnerabilities in systems previously thought secure.</p><h3>Essential Cybersecurity Practices</h3><ul><li>Strong password management and multi-factor authentication</li><li>Regular software updates and security patches</li><li>Awareness of phishing and social engineering tactics</li><li>Secure backup strategies for important data</li></ul><p>Businesses are investing heavily in cybersecurity infrastructure and employee training. The shortage of cybersecurity professionals has created opportunities for career development in this critical field.</p><p>Artificial intelligence is being deployed both by attackers and defenders, creating an arms race in cyber capabilities. Machine learning algorithms can detect unusual patterns and potential threats in real-time.</p>",
                'description' => 'Comprehensive guide to cybersecurity threats and protection strategies in our digital world.',
                'excerpt' => 'Stay secure in the digital age with essential cybersecurity practices and threat awareness.',
                'categories' => ['technology'],
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'Sustainable Living: Small Changes, Big Impact',
                'content' => "<p>Sustainable living has moved from a niche lifestyle choice to a mainstream movement as more people recognize the impact of individual actions on environmental health.</p><p>Simple changes in daily routines can significantly reduce carbon footprints and environmental impact. From energy-efficient appliances to sustainable transportation choices, consumers have more options than ever to live responsibly.</p><h3>Practical Sustainability Tips</h3><ul><li>Reducing single-use plastics and packaging waste</li><li>Choosing renewable energy sources for homes</li><li>Supporting local and organic food producers</li><li>Implementing water conservation techniques</li></ul><p>Communities are creating support networks for sustainable living through tool libraries, community gardens, and skill-sharing programs. These initiatives build social connections while promoting environmental responsibility.</p><p>The circular economy concept is gaining traction, with businesses designing products for longevity, repairability, and recyclability. Consumers are embracing the sharing economy and secondhand markets as sustainable alternatives.</p>",
                'description' => 'Practical guide to sustainable living practices that make a meaningful environmental impact.',
                'excerpt' => 'Learn how small changes in daily life can contribute to a more sustainable future for everyone.',
                'categories' => ['health', 'science'],
                'published_at' => now()->subDays(8),
            ],
        ];

        $createdPosts = [];
        foreach ($postsData as $index => $postData) {
            $post = Post::firstOrCreate([
                'slug' => Str::slug($postData['title'])
            ], [
                'title' => $postData['title'],
                'content' => $postData['content'],
                'description' => $postData['description'],
                'excerpt' => $postData['excerpt'],
                'published' => true,
                'published_at' => $postData['published_at'],
                'user_id' => $index % 2 === 0 ? $admin->id : $testUser->id,
            ]);

            // Attach categories
            $categoryIds = [];
            foreach ($postData['categories'] as $categorySlug) {
                $category = collect($categories)->firstWhere('slug', $categorySlug);
                if ($category) {
                    $categoryIds[] = $category->id;
                }
            }

            if (!empty($categoryIds)) {
                $post->categories()->sync($categoryIds);
            }

            $createdPosts[] = $post;
        }

        $this->command->info('✅ Posts created: ' . count($createdPosts));

        // Create additional posts for pagination testing
        $additionalPosts = [
            'Breaking: New Scientific Discovery Changes Everything We Know',
            'Market Analysis: Tech Stocks Surge Amid AI Revolution',
            'Health Update: Revolutionary Treatment Shows Promise',
            'Sports News: Championship Finals Draw Record Viewership',
            'Political Update: New Climate Legislation Passes',
            'Technology Review: Latest Smartphone Features Compared',
            'Business Insight: Remote Work Trends Continue to Evolve',
            'Science Update: Deep Space Telescope Reveals New Galaxies',
            'Health Alert: Seasonal Flu Prevention Tips',
            'Sports Feature: Olympic Preparations Underway',
        ];

        foreach ($additionalPosts as $index => $title) {
            $slug = Str::slug($title);
            $post = Post::firstOrCreate([
                'slug' => $slug
            ], [
                'title' => $title,
                'content' => "<p>This is a sample article about " . strtolower($title) . ". Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p><p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.</p>",
                'description' => 'A comprehensive article covering ' . strtolower($title) . ' and its implications.',
                'excerpt' => substr($title, 0, 100) . '...',
                'published' => true,
                'published_at' => now()->subDays(9 + $index),
                'user_id' => $index % 2 === 0 ? $admin->id : $testUser->id,
            ]);

            // Assign random category
            $randomCategory = $categories[array_rand($categories)];
            $post->categories()->sync([$randomCategory->id]);
        }

        $this->command->info('✅ Additional posts created: ' . count($additionalPosts));

        // Final statistics
        $totalPosts = Post::count();
        $totalCategories = Category::count();
        $totalUsers = User::count();

        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->info("📊 Final Statistics:");
        $this->command->info("   - Users: {$totalUsers}");
        $this->command->info("   - Categories: {$totalCategories}");
        $this->command->info("   - Posts: {$totalPosts}");
        $this->command->info("🔑 Login credentials:");
        $this->command->info("   - Admin: admin@example.com / password");
        $this->command->info("   - Test: test@example.com / password");
    }
}
