<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Laravel CMS'],
            ['key' => 'site_description', 'value' => 'A powerful content management system built with Laravel'],
            ['key' => 'site_email', 'value' => 'admin@example.com'],
            ['key' => 'posts_per_page', 'value' => '10'],
            ['key' => 'enable_comments', 'value' => 'true'],
            ['key' => 'maintenance_mode', 'value' => 'false'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::create($setting);
        }
    }
}
