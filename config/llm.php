<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Octavia LLM Layer
    |--------------------------------------------------------------------------
    |
    | Octavia talks to Large Language Models through a provider abstraction.
    | The default "mock" provider is deterministic and needs no credentials,
    | which keeps local development, CI and the demo experience fully
    | functional. Point OPENAI_API_KEY (and optionally OPENAI_BASE_URL) at
    | any OpenAI-compatible endpoint to use a real model.
    |
    */

    'default' => env('OCTAVIA_LLM_PROVIDER', 'mock'),

    'providers' => [

        'mock' => [
            'driver' => 'mock',
        ],

        'openai' => [
            'driver' => 'openai',
            'key' => env('OPENAI_API_KEY'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'timeout' => env('OPENAI_TIMEOUT', 60),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Evolution engine defaults
    |--------------------------------------------------------------------------
    */

    'evolution' => [
        'max_steps' => env('OCTAVIA_EVOLUTION_MAX_STEPS', 8),
        'target_score' => env('OCTAVIA_EVOLUTION_TARGET_SCORE', 0.95),
        'stale_steps' => env('OCTAVIA_EVOLUTION_STALE_STEPS', 3),
    ],

    // Per-user daily run creation quota (cost control).
    'run_quota_per_day' => env('OCTAVIA_RUN_QUOTA_PER_DAY', 50),

    // Credits: 1 credit = one engine step. Runs reserve max_steps up front.
    'signup_credits' => env('OCTAVIA_SIGNUP_CREDITS', 100),

    'cost_optimized' => [
        'enabled' => (bool) env('OCTAVIA_COST_OPTIMIZED', false),
        'mutation_model' => env('OCTAVIA_CHEAP_MODEL', 'gpt-4o-mini'),
        'evaluation_model' => env('OCTAVIA_STRONG_MODEL', 'gpt-4o'),
    ],
];
