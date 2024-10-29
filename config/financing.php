<?php

declare(strict_types=1);

return [
    /**
     * This is connection database must be available in database config
     */
    'connection' => env('DISTRIBUTION_FINANCING_CONNECTION', env('DB_CONNECTION', 'sqlite')),

    /**
     * This is table name for financing
     */
    'table' => [
        'distribution_financings' => env('DISTRIBUTION_FINANCING_TABLE_FINANCING', 'distribution_financings'),

        'donations' => env('DISTRIBUTION_FINANCING_TABLE_DONATIONS', 'donations'),

        'distributions' => env('DISTRIBUTION_FINANCING_TABLE_DISTRIBUTIONS', 'distributions'),
    ],

    /**
     * Indicated must be running migration, internally used in testing
     */
    'migration' => env('DISTRIBUTION_FINANCING_MIGRATION', false),

    /**
     * This is model name for financing
     */
    'models' => [
        'donation' => \Inisiatif\Distribution\Financings\Models\Donation::class,

        'distribution' => \Inisiatif\Distribution\Financings\Models\Distribution::class,
    ],
];
