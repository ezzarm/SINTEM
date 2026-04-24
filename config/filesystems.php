<?php

return [

    'default' => env('FILESYSTEM_DISK', 'supabase'),

    'disks' => [

        'supabase' => [
            'driver'     => 'custom',
            'via'        => App\Filesystem\SupabaseStorageAdapter::class,
            'url'        => env('SUPABASE_URL'),
            'key'        => env('SUPABASE_KEY'),
            'bucket'     => env('SUPABASE_STORAGE_BUCKET', 'sintem-files'),
            'visibility' => 'public',
        ],

    ],

    'links' => [],

];
