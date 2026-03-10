<?php

return [
    'items' => [
        // Promotion Plans
        'promo_weekly' => [
            'type'  => 'promotion',
            'name'  => 'Weekly Boost',
            'price' => 499,
        ],
        'promo_monthly' => [
            'type'  => 'promotion',
            'name'  => 'Monthly Pro',
            'price' => 1499,
        ],

        // === BINA PROMOTION WALA PRODUCT ===
        'registration_fee' => [
            'type'  => 'one-time-fee',
            'name'  => 'Lifetime Membership',
            'price' => 2999, // Example price
        ],
    ],
];
