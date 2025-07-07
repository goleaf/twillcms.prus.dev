<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;
use App\Models\Tag;
use App\Models\Category;

class NewsPortalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedSiteSettings();
        $this->seedTags();
        $this->seedCategories();
    }

    private function seedSiteSettings(): void
    {
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'News Portal', 'type' => 'string', 'group' => 'general', 'is_public' => true, 'description' => 'Site name displayed in header'],
            ['key' => 'site_tagline', 'value' => 'Your trusted source for news', 'type' => 'string', 'group' => 'general', 'is_public' => true, 'description' => 'Site tagline'],
            ['key' => 'site_description', 'value' => 'A modern news portal built with Laravel and Twill CMS', 'type' => 'string', 'group' => 'general', 'is_public' => true, 'description' => 'Site meta description'],
            ['key' => 'posts_per_page', 'value' => '12', 'type' => 'integer', 'group' => 'general', 'is_public' => true, 'description' => 'Number of posts per page'],
            ['key' => 'comments_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'general', 'is_public' => true, 'description' => 'Enable comments on posts'],
            ['key' => 'comments_moderation', 'value' => '1', 'type' => 'boolean', 'group' => 'general', 'is_public' => false, 'description' => 'Moderate comments before publishing'],

            // Social Media
            ['key' => 'facebook_url', 'value' => '', 'type' => 'string', 'group' => 'social', 'is_public' => true, 'description' => 'Facebook page URL'],
            ['key' => 'twitter_url', 'value' => '', 'type' => 'string', 'group' => 'social', 'is_public' => true, 'description' => 'Twitter profile URL'],
            ['key' => 'instagram_url', 'value' => '', 'type' => 'string', 'group' => 'social', 'is_public' => true, 'description' => 'Instagram profile URL'],
            ['key' => 'linkedin_url', 'value' => '', 'type' => 'string', 'group' => 'social', 'is_public' => true, 'description' => 'LinkedIn profile URL'],

            // Newsletter
            ['key' => 'newsletter_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'newsletter', 'is_public' => true, 'description' => 'Enable newsletter subscription'],
            ['key' => 'newsletter_welcome_subject', 'value' => 'Welcome to our newsletter!', 'type' => 'string', 'group' => 'newsletter', 'is_public' => false, 'description' => 'Newsletter welcome email subject'],
            ['key' => 'newsletter_welcome_message', 'value' => 'Thank you for subscribing to our newsletter. Stay tuned for the latest news!', 'type' => 'string', 'group' => 'newsletter', 'is_public' => false, 'description' => 'Newsletter welcome email message'],

            // SEO
            ['key' => 'google_analytics_id', 'value' => '', 'type' => 'string', 'group' => 'seo', 'is_public' => false, 'description' => 'Google Analytics tracking ID'],
            ['key' => 'meta_keywords', 'value' => 'news, portal, cms, laravel, twill', 'type' => 'string', 'group' => 'seo', 'is_public' => true, 'description' => 'Default meta keywords'],

            // Contact
            ['key' => 'contact_email', 'value' => 'info@newsportal.com', 'type' => 'string', 'group' => 'contact', 'is_public' => true, 'description' => 'Contact email address'],
            ['key' => 'contact_phone', 'value' => '', 'type' => 'string', 'group' => 'contact', 'is_public' => true, 'description' => 'Contact phone number'],
            ['key' => 'contact_address', 'value' => '', 'type' => 'string', 'group' => 'contact', 'is_public' => true, 'description' => 'Contact address'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    private function seedTags(): void
    {
        $tags = [
            ['name' => 'Breaking News', 'color' => '#dc2626', 'is_featured' => true],
            ['name' => 'Politics', 'color' => '#2563eb', 'is_featured' => true],
            ['name' => 'Technology', 'color' => '#059669', 'is_featured' => true],
            ['name' => 'Sports', 'color' => '#ea580c', 'is_featured' => true],
            ['name' => 'Business', 'color' => '#7c3aed', 'is_featured' => true],
            ['name' => 'Health', 'color' => '#dc2626', 'is_featured' => false],
            ['name' => 'Education', 'color' => '#2563eb', 'is_featured' => false],
            ['name' => 'Entertainment', 'color' => '#ec4899', 'is_featured' => false],
            ['name' => 'Science', 'color' => '#059669', 'is_featured' => false],
            ['name' => 'Travel', 'color' => '#ea580c', 'is_featured' => false],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['name' => $tag['name']],
                $tag
            );
        }
    }

    private function seedCategories(): void
    {
        // This method can be used to seed additional categories if needed
        // The existing Category model and seeder should handle basic categories
    }
}
