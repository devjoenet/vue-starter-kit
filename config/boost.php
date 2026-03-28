<?php

declare(strict_types=1);

return array_replace_recursive(
    require base_path('vendor/laravel/boost/config/boost.php'),
    [
        'guidelines' => [
            'exclude' => [
                'boost',
                'herd',
                'inertia-laravel/core',
            ],
        ],
    ],
);
