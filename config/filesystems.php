<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Define qual disco o Laravel deve usar por padrão. No ambiente de produção,
    | o R2 (via driver s3) é o recomendado. Localmente, pode continuar usando o
    | disco local.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Cada "disk" representa um tipo de armazenamento. Aqui estão configurados:
    | - local: usado para desenvolvimento/teste
    | - public: acessível via /storage
    | - s3: usado para o Cloudflare R2 (ou AWS S3)
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'auto'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => true, // Necessário para Cloudflare R2
            'visibility' => 'public', // deixa os arquivos acessíveis via URL
            'throw' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Define os links simbólicos criados com o comando `php artisan storage:link`.
    | Isso permite acesso a arquivos armazenados localmente em /storage.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
