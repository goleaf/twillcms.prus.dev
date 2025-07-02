<?php

return [
    'enabled' => [
        'users-management' => true,
        'media-library' => true,
        'file-library' => true,
        'block-editor' => true,
        'buckets' => false,
        'users-oauth' => false,
        'activitylog' => false,
        'dashboard' => true,
        'search' => false,
        'settings' => false,
    ],
    
    'locales' => [
        'en' => [
            'label' => 'English',
            'regional' => 'en_US',
        ],
    ],
    
    'locale' => 'en',
    'fallback_locale' => 'en',
    
    'translatable' => [
        'use_subdomain_locale_detection' => false,
        'use_browser_locale_detection' => false,
        'use_accept_language_header' => false,
        'subdomain_prefix' => null,
        'use_url_locale_detection' => false,
        'locale_key' => 'locale',
        'supported_locales' => ['en'],
        'enabled' => false,
    ],
    
    'modules' => [
        'posts' => [
            'translatable' => false,
        ],
        'categories' => [
            'translatable' => false,
        ],
    ],
];
