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
        'donation' => [
            'model' => env('DISTRIBUTION_FINANCING_TABLE_DONATIONS', 'donations'),

            'detail' => env('DISTRIBUTION_FINANCING_TABLE_DONATION_DETAILS', 'donation_details'),

            'program' => env('DISTRIBUTION_FINANCING_TABLE_DONATION_PROGRAMS', 'programs'),

            'funding_type' => env('DISTRIBUTION_FINANCING_TABLE_DONATION_FUNDING_TYPES', 'funding_types'),
        ],

        'distribution' => [
            'model' => env('DISTRIBUTION_FINANCING_TABLE_DISTRIBUTIONS', 'distributions'),

            'financing' => env('DISTRIBUTION_FINANCING_TABLE_FINANCING', 'distribution_financings'),
        ],
    ],

    /**
     * Indicated must be running migration, internally used in testing
     */
    'migration' => env('DISTRIBUTION_FINANCING_MIGRATION', false),

    /**
     * This is model name for financing
     */
    'models' => [
        'donation' => [
            'model' => \Inisiatif\Distribution\Financings\Models\Donation::class,

            'detail' => \Inisiatif\Distribution\Financings\Models\DonationDetail::class,

            'program' => \Inisiatif\Distribution\Financings\Models\DonationProgram::class,

            'funding_type' => \Inisiatif\Distribution\Financings\Models\DonationFundingType::class,
        ],

        'distribution' => [
            'model' => \Inisiatif\Distribution\Financings\Models\Distribution::class,

            'program' => \Inisiatif\Distribution\Financings\Models\Program::class,

            'program_sector' => \Inisiatif\Distribution\Financings\Models\ProgramSector::class,
        ],
    ],
];
