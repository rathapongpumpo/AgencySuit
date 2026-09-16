<?php

return [
    'transaction_types' => [
        'sale' => 'ขาย',
        'rent' => 'เช่า',
    ],

    'statuses' => [
        'available' => [
            'sale' => 'พร้อมขาย',
            'rent' => 'พร้อมเช่า',
        ],
        'reserved' => 'จองแล้ว',
        'completed' => [
            'sale' => 'ขายแล้ว',
            'rent' => 'ปล่อยแล้ว',
        ],
        'paused' => 'พักประกาศ',
    ],
];
