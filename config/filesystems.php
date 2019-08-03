<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
        'upload' => [
            'driver' => 'local',
            'root' => storage_path('app/public/content/original'),
            'relroot' => ('app/public/content/original'),
            'storageUrl' => ('content/original'),
            // 'url' => env('APP_URL').'/storage',
        ],

        'thumb-360' => [
            'driver' => 'local',
            'root' => storage_path('app/public/content/thumb-360'),
        ],
        'thumb-640' => [
            'driver' => 'local',
            'root' => storage_path('app/public/content/thumb-640'),
            'storageUrl' => ('content/original'),
        ],
        'avatars' => [
            'driver' => 'local',
            'root' => storage_path('app/public/avatars/original'),
            'storageUrl' => ('avatars/original'),
            // 'url' => env('APP_URL').'/storage',
        ],
        'avatars_thumb-50' => [
            'driver' => 'local',
            'root' => storage_path('app/public/avatars/thumb-50'),
            'storageUrl' => ('avatars/thumb-50'),
        ],
        'avatars_thumb-360' => [
            'driver' => 'local',
            'root' => storage_path('app/public/avatars/thumb-360'),
            'storageUrl' => ('avatars/thumb-360'),
        ],
        'avatars_thumb-640' => [
            'driver' => 'local',
            'root' => storage_path('app/public/avatars/thumb-640'),
            'storageUrl' => ('avatars/thumb-640'),
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

    ],

];
