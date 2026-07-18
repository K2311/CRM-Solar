<?php

return [
    'demo' => [
        'name' => 'Demo Trial Plan',
        'price' => 0,
        'user_limit' => 3,
        'lead_limit' => 15,
        'features' => [
            'whatsapp_templates' => false,
            'branding' => false,
        ]
    ],
    'pro' => [
        'name' => 'Pro Solar Plan',
        'price' => 49, // USD per month
        'user_limit' => 10,
        'lead_limit' => 500,
        'features' => [
            'whatsapp_templates' => true,
            'branding' => true,
        ]
    ],
    'enterprise' => [
        'name' => 'Enterprise Enterprise Plan',
        'price' => 149, // USD per month
        'user_limit' => 999, // unlimited
        'lead_limit' => 99999, // unlimited
        'features' => [
            'whatsapp_templates' => true,
            'branding' => true,
        ]
    ]
];
