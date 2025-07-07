<?php

// Bootstrap file for tests
require_once __DIR__ . '/../vendor/autoload.php';

// Set up testing environment
$_ENV['APP_ENV'] = 'testing';
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = ':memory:';

// Load Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Ensure we're in testing mode
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
