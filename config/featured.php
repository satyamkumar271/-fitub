<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Featured Professionals (Home Page)
    |--------------------------------------------------------------------------
    |
    | Admin can mark verified trainers/gym owners as "featured" for a limited
    | time. Home page will only show currently-active featured profiles.
    |
    */

    'eligible_plans' => ['pro', 'business'],

    'home_limits' => [
        'trainer' => 6,
        'gymowner' => 6,
    ],

    /*
    |--------------------------------------------------------------------------
    | Promo / Trial Featured
    |--------------------------------------------------------------------------
    |
    | Allow admin to feature non-eligible professionals for limited days
    | (e.g. onboarding / outreach offers).
    |
    */
    'promo' => [
        'allowed_days' => [2, 3, 7],
        'max_total_days_per_user' => 7,
        'max_grants_per_user' => 1,
        'cooldown_days' => 30,
    ],
];
