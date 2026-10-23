<?php

// Serverless entrypoint for Vercel
$storagePath = '/tmp/storage';

$dirs = [
    $storagePath.'/app/public',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

putenv('APP_STORAGE='.$storagePath);
putenv('VIEW_COMPILED_PATH='.$storagePath.'/framework/views');

require __DIR__.'/../public/index.php';
