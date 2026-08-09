<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Create temporary storage directories in /tmp for Vercel Serverless environment
$tmpStorage = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/testing',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($tmpStorage as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Fallback APP_KEY if not configured in Vercel UI
if (!getenv('APP_KEY') && empty($_ENV['APP_KEY'])) {
    putenv('APP_KEY=base64:hY6QE4DXLBB9ak0hAvBfR1jReRSDCEvI96WHmEkZ6vM=');
    $_ENV['APP_KEY'] = 'base64:hY6QE4DXLBB9ak0hAvBfR1jReRSDCEvI96WHmEkZ6vM=';
}

putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');
putenv('LOG_CHANNEL=stderr');

$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_ENV['SESSION_DRIVER'] = 'cookie';
$_ENV['CACHE_STORE'] = 'array';
$_ENV['LOG_CHANNEL'] = 'stderr';

// Register Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel Application
/** @var Application $app */
$app = require __DIR__ . '/../bootstrap/app.php';

// Set storage path explicitly to writable /tmp/storage
$app->useStoragePath('/tmp/storage');

// Handle and send response
$request = Request::capture();
$response = $app->handleRequest($request);
$response->send();
