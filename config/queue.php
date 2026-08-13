<?php

return [
    'default' => env('QUEUE_CONNECTION', 'sync'),

    'connections' => [
        'sync' => ['driver'=>'sync'],
        'redis' => [
            'driver'=>'redis',
            'connection'=>env('REDIS_QUEUE_CONNECTION','default'),
            'queue'=>env('REDIS_QUEUE','default'),
            'retry_after'=>env('QUEUE_RETRY_AFTER',90),
            'block_for'=>null,
        ],
    ],

    'failed' => [
        'driver'=>env('QUEUE_FAILED_DRIVER','none'),
    ],
];
