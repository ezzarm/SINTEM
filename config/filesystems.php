<?php
return [
    'default' => env('FILESYSTEM_DISK', 'supabase'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'supabase' => [
            'driver' => 's3',
            'key' => env('SUPABASE_KEY'),
            'secret' => env('SUPABASE_KEY'), // Supabase pakai key yang sama
            'region' => 'ap-southeast-1',
            'bucket' => env('SUPABASE_BUCKET'),
            'url' => env('SUPABASE_URL') . '/storage/v1/s3',
            'endpoint' => env('SUPABASE_URL') . '/storage/v1/s3',
            'use_path_style_endpoint' => true,
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],
    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
