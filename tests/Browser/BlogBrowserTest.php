<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BlogBrowserTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_view_blog_homepage()
    {
        // Create test data
        $post = Post::create([
            'published' => true,
            'position' => 1,
        ]);

        $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Welcome to Our Blog',
            'description' => 'This is our first blog post',
            'content' => '<p>Welcome to our amazing blog!</p>',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Welcome to Our Blog')
                ->assertSee('This is our first blog post')
                ->assertPresent('.blog-post')
                ->assertPresent('.navigation');
        });
    }

    public function test_user_can_navigate_to_post_detail()
    {
        $post = Post::create([
            'published' => true,
            'position' => 1,
        ]);

        $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Detailed Blog Post',
            'description' => 'Click to read more',
            'content' => '<p>This is the full content of the blog post.</p>',
        ]);

        $this->browse(function (Browser $browser) use ($post) {
            $browser->visit('/')
                ->assertSee('Detailed Blog Post')
                ->click('.blog-post .read-more')
                ->assertPathIs("/post/{$post->id}")
                ->assertSee('This is the full content of the blog post')
                ->assertPresent('.post-content');
        });
    }

    public function test_user_can_search_for_posts()
    {
        // Create multiple posts
        $post1 = Post::create(['published' => true, 'position' => 1]);
        $post1->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Laravel Tutorial',
            'description' => 'Learn Laravel framework',
            'content' => '<p>Laravel is amazing</p>',
        ]);

        $post2 = Post::create(['published' => true, 'position' => 2]);
        $post2->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'React Guide',
            'description' => 'Learn React library',
            'content' => '<p>React is powerful</p>',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->type('input[name="search"]', 'Laravel')
                ->press('Search')
                ->assertSee('Laravel Tutorial')
                ->assertDontSee('React Guide');
        });
    }

    public function test_user_can_filter_by_category()
    {
        // Create categories
        $techCategory = Category::create(['published' => true, 'position' => 1]);
        $techCategory->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Technology',
            'description' => 'Tech posts',
        ]);

        $designCategory = Category::create(['published' => true, 'position' => 2]);
        $designCategory->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Design',
            'description' => 'Design posts',
        ]);

        // Create posts
        $techPost = Post::create(['published' => true, 'position' => 1]);
        $techPost->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'AI Technology',
            'description' => 'About AI',
            'content' => '<p>AI content</p>',
        ]);
        $techPost->categories()->attach($techCategory->id);

        $designPost = Post::create(['published' => true, 'position' => 2]);
        $designPost->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'UI Design',
            'description' => 'About design',
            'content' => '<p>Design content</p>',
        ]);
        $designPost->categories()->attach($designCategory->id);

        $this->browse(function (Browser $browser) use ($techCategory) {
            $browser->visit('/')
                ->click('.category-filter')
                ->click("a[href='/category/{$techCategory->id}']")
                ->assertSee('AI Technology')
                ->assertDontSee('UI Design');
        });
    }

    public function test_user_can_switch_languages()
    {
        $post = Post::create(['published' => true, 'position' => 1]);

        // English translation
        $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'English Post',
            'description' => 'English description',
            'content' => '<p>English content</p>',
        ]);

        // Lithuanian translation
        $post->translations()->create([
            'locale' => 'lt',
            'active' => true,
            'title' => 'Lietuviškas įrašas',
            'description' => 'Lietuviškas aprašymas',
            'content' => '<p>Lietuviškas turinys</p>',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('English Post')
                ->click('.language-switcher .lt-link')
                ->assertSee('Lietuviškas įrašas')
                ->assertDontSee('English Post');
        });
    }

    public function test_responsive_design_on_mobile()
    {
        $post = Post::create(['published' => true, 'position' => 1]);
        $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Mobile Test Post',
            'description' => 'Testing mobile view',
            'content' => '<p>Mobile content</p>',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->resize(375, 667) // iPhone size
                ->visit('/')
                ->assertSee('Mobile Test Post')
                ->assertPresent('.mobile-menu-toggle')
                ->click('.mobile-menu-toggle')
                ->assertVisible('.mobile-menu');
        });
    }

    public function test_pagination_works_in_browser()
    {
        // Create 25 posts to trigger pagination
        for ($i = 1; $i <= 25; $i++) {
            $post = Post::create(['published' => true, 'position' => $i]);
            $post->translations()->create([
                'locale' => 'en',
                'active' => true,
                'title' => "Post {$i}",
                'description' => "Description {$i}",
                'content' => "<p>Content {$i}</p>",
            ]);
        }

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Post 1')
                ->assertPresent('.pagination')
                ->click('.pagination .next')
                ->assertPathIs('/?page=2')
                ->assertSee('Post 13') // Assuming 12 posts per page
                ->assertDontSee('Post 1');
        });
    }

    public function test_social_sharing_buttons_work()
    {
        $post = Post::create(['published' => true, 'position' => 1]);
        $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Shareable Post',
            'description' => 'Share this post',
            'content' => '<p>Great content to share</p>',
        ]);

        $this->browse(function (Browser $browser) use ($post) {
            $browser->visit("/post/{$post->id}")
                ->assertPresent('.social-share')
                ->assertPresent('.share-twitter')
                ->assertPresent('.share-facebook')
                ->assertPresent('.share-linkedin');
        });
    }

    public function test_newsletter_subscription_form()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->scrollIntoView('.newsletter-signup')
                ->type('input[name="email"]', 'test@example.com')
                ->press('Subscribe')
                ->assertSee('Thank you for subscribing');
        });
    }

    public function test_error_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/nonexistent-page')
                ->assertSee('404')
                ->assertSee('Page Not Found')
                ->assertPresent('.error-page')
                ->assertPresent('a[href="/"]'); // Home link
        });
    }

    public function test_loading_states_and_performance()
    {
        // Create many posts to test loading
        for ($i = 1; $i <= 50; $i++) {
            $post = Post::create(['published' => true, 'position' => $i]);
            $post->translations()->create([
                'locale' => 'en',
                'active' => true,
                'title' => "Performance Test Post {$i}",
                'description' => "Description {$i}",
                'content' => "<p>Content {$i}</p>",
            ]);
        }

        $this->browse(function (Browser $browser) {
            $start = microtime(true);

            $browser->visit('/')
                ->assertSee('Performance Test Post 1');

            $loadTime = microtime(true) - $start;

            // Assert page loads in reasonable time (less than 3 seconds)
            $this->assertLessThan(3, $loadTime, 'Page should load in less than 3 seconds');
        });
    }

    public function test_accessibility_features()
    {
        $post = Post::create(['published' => true, 'position' => 1]);
        $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Accessibility Test',
            'description' => 'Testing accessibility',
            'content' => '<p>Accessible content</p>',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertPresent('[alt]') // Images should have alt text
                ->assertPresent('[aria-label]') // ARIA labels
                ->keys('body', '{tab}') // Test keyboard navigation
                ->assertFocused('.skip-link'); // Skip link should be focusable
        });
    }
}
