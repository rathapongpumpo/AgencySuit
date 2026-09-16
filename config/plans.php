<?php

return [
    'free' => [
        'limits' => [
            'properties' => 10,
            'clients' => 5,
            'photos_per_property' => 3,
            'active_deals' => 1,
        ],
    ],

    'pro' => [
        // Paid limits are intentionally configured here later.
        'limits' => [],
    ],
];
