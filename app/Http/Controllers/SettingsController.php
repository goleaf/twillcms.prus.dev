<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = $this->getSettings();

        return view('settings.index', compact('settings'));
    }

    /**
     * Update site settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_title' => 'required|string|max:255',
            'site_description' => 'required|string|max:500',
            'site_keywords' => 'nullable|string|max:255',
            'posts_per_page' => 'required|integer|min:1|max:50',
            'default_language' => 'required|in:en,lt',
            'enable_comments' => 'boolean',
            'enable_newsletter' => 'boolean',
            'enable_social_sharing' => 'boolean',
            'google_analytics_id' => 'nullable|string|max:50',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'contact_email' => 'nullable|email',
            'maintenance_mode' => 'boolean',
            'cache_enabled' => 'boolean',
            'cache_duration' => 'required|integer|min:1|max:1440',
            'theme_color' => 'required|string|max:7',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $settings = $this->getSettings();

            // Update basic settings
            $settings['site_title'] = $request->input('site_title');
            $settings['site_description'] = $request->input('site_description');
            $settings['site_keywords'] = $request->input('site_keywords');
            $settings['posts_per_page'] = (int) $request->input('posts_per_page');
            $settings['default_language'] = $request->input('default_language');

            // Update feature toggles
            $settings['enable_comments'] = $request->boolean('enable_comments');
            $settings['enable_newsletter'] = $request->boolean('enable_newsletter');
            $settings['enable_social_sharing'] = $request->boolean('enable_social_sharing');

            // Update integrations
            $settings['google_analytics_id'] = $request->input('google_analytics_id');
            $settings['facebook_url'] = $request->input('facebook_url');
            $settings['twitter_url'] = $request->input('twitter_url');
            $settings['instagram_url'] = $request->input('instagram_url');
            $settings['linkedin_url'] = $request->input('linkedin_url');
            $settings['contact_email'] = $request->input('contact_email');

            // Update system settings
            $settings['maintenance_mode'] = $request->boolean('maintenance_mode');
            $settings['cache_enabled'] = $request->boolean('cache_enabled');
            $settings['cache_duration'] = (int) $request->input('cache_duration');
            $settings['theme_color'] = $request->input('theme_color');

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('public/images/site');
                $settings['logo_path'] = str_replace('public/', '', $logoPath);
            }

            // Save settings
            $this->saveSettings($settings);

            // Clear cache if cache settings changed
            if ($request->boolean('cache_enabled') !== $this->getSetting('cache_enabled')) {
                Cache::flush();
            }

            return redirect()->back()->with('success', __('settings.updated_successfully'));

        } catch (\Exception $e) {
            Log::error('Settings update failed: '.$e->getMessage());

            return redirect()->back()
                ->with('error', __('settings.update_failed'))
                ->withInput();
        }
    }

    /**
     * Reset settings to defaults
     */
    public function reset()
    {
        try {
            $defaultSettings = $this->getDefaultSettings();
            $this->saveSettings($defaultSettings);

            Cache::flush();

            return redirect()->back()->with('success', __('settings.reset_successfully'));

        } catch (\Exception $e) {
            Log::error('Settings reset failed: '.$e->getMessage());

            return redirect()->back()->with('error', __('settings.reset_failed'));
        }
    }

    /**
     * Export settings as JSON
     */
    public function export()
    {
        try {
            $settings = $this->getSettings();

            $filename = 'blog_settings_'.date('Y-m-d_H-i-s').'.json';
            $content = json_encode($settings, JSON_PRETTY_PRINT);

            return response($content)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');

        } catch (\Exception $e) {
            Log::error('Settings export failed: '.$e->getMessage());

            return redirect()->back()->with('error', __('settings.export_failed'));
        }
    }

    /**
     * Import settings from JSON file
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings_file' => 'required|file|mimes:json|max:1024',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            $file = $request->file('settings_file');
            $content = file_get_contents($file->getPathname());
            $importedSettings = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON format');
            }

            // Validate imported settings structure
            $this->validateImportedSettings($importedSettings);

            // Merge with current settings (to preserve any new fields)
            $currentSettings = $this->getSettings();
            $mergedSettings = array_merge($currentSettings, $importedSettings);

            $this->saveSettings($mergedSettings);

            Cache::flush();

            return redirect()->back()->with('success', __('settings.imported_successfully'));

        } catch (\Exception $e) {
            Log::error('Settings import failed: '.$e->getMessage());

            return redirect()->back()->with('error', __('settings.import_failed').': '.$e->getMessage());
        }
    }

    /**
     * Get all settings
     */
    private function getSettings()
    {
        $settingsFile = storage_path('app/settings.json');

        if (! file_exists($settingsFile)) {
            return $this->getDefaultSettings();
        }

        $settings = json_decode(file_get_contents($settingsFile), true);

        if (! $settings) {
            return $this->getDefaultSettings();
        }

        // Merge with defaults to ensure all settings exist
        return array_merge($this->getDefaultSettings(), $settings);
    }

    /**
     * Get a specific setting
     */
    private function getSetting($key, $default = null)
    {
        $settings = $this->getSettings();

        return $settings[$key] ?? $default;
    }

    /**
     * Save settings to file
     */
    private function saveSettings($settings)
    {
        $settingsFile = storage_path('app/settings.json');
        file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
    }

    /**
     * Get default settings
     */
    private function getDefaultSettings()
    {
        return [
            'site_title' => 'TwillCMS Blog',
            'site_description' => 'A modern blog built with TwillCMS and Laravel',
            'site_keywords' => 'blog, cms, laravel, twill',
            'posts_per_page' => 12,
            'default_language' => 'en',
            'enable_comments' => true,
            'enable_newsletter' => true,
            'enable_social_sharing' => true,
            'google_analytics_id' => '',
            'facebook_url' => '',
            'twitter_url' => '',
            'instagram_url' => '',
            'linkedin_url' => '',
            'contact_email' => '',
            'maintenance_mode' => false,
            'cache_enabled' => true,
            'cache_duration' => 60, // minutes
            'theme_color' => '#3B82F6',
            'logo_path' => '',
        ];
    }

    /**
     * Validate imported settings structure
     */
    private function validateImportedSettings($settings)
    {
        $requiredKeys = [
            'site_title',
            'site_description',
            'posts_per_page',
            'default_language',
        ];

        foreach ($requiredKeys as $key) {
            if (! isset($settings[$key])) {
                throw new \Exception("Missing required setting: {$key}");
            }
        }

        // Validate data types
        if (! is_string($settings['site_title']) || empty($settings['site_title'])) {
            throw new \Exception('site_title must be a non-empty string');
        }

        if (! is_int($settings['posts_per_page']) || $settings['posts_per_page'] < 1) {
            throw new \Exception('posts_per_page must be a positive integer');
        }

        if (! in_array($settings['default_language'], ['en', 'lt'])) {
            throw new \Exception('default_language must be either "en" or "lt"');
        }
    }

    /**
     * Get settings for public use (helper method for views)
     */
    public static function getPublicSettings()
    {
        $controller = new self;

        return $controller->getSettings();
    }
}
