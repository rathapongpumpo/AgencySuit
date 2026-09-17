<?php

return [
    'weights' => [
        'price' => 30,
        'location' => 25,
        'transaction_type' => 10,
        'bedrooms' => 10,
        'size' => 10,
        'transit' => 10,
        'other' => 5,
    ],

    // The current Client schema stores one budget value, treated as the maximum budget.
    'price' => [
        'mode' => 'maximum_budget',
        'over_budget_score' => 'budget_to_price_ratio',
    ],
];
