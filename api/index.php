<?php

// Serverless entrypoint for Vercel
$storagePath = '/tmp/storage';
$bootstrapCachePath = '/tmp/bootstrap/cache';

$dirs = [
    $storagePath.'/app/public',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
    $bootstrapCachePath,
];

foreach ($dirs as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

putenv('APP_STORAGE='.$storagePath);
putenv('VIEW_COMPILED_PATH='.$storagePath.'/framework/views');
putenv('APP_SERVICES_CACHE='.$bootstrapCachePath.'/services.php');
putenv('APP_PACKAGES_CACHE='.$bootstrapCachePath.'/packages.php');
putenv('APP_CONFIG_CACHE='.$bootstrapCachePath.'/config.php');
putenv('APP_ROUTES_CACHE='.$bootstrapCachePath.'/routes-v7.php');
putenv('APP_EVENTS_CACHE='.$bootstrapCachePath.'/events.php');

require __DIR__.'/../public/index.php';
