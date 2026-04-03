<?php

declare(strict_types=1);

return [
    'observability' => [
        'slow_query_threshold_ms' => (int) env('SLOW_QUERY_THRESHOLD_MS', 120),
        'vite_prefetch_concurrency' => 3,
    ],
    'budgets' => [
        'query_count' => [
            'admin_dashboard' => 8,
            'admin_users_index' => 12,
            'admin_roles_index' => 5,
            'admin_permissions_index' => 8,
        ],
        'response_bytes' => [
            'welcome_page' => 24_576,
            'admin_dashboard' => 24_576,
            'admin_users_index' => 28_672,
        ],
    ],
];
