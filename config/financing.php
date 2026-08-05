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

        'donation_details' => env('DISTRIBUTION_FINANCING_TABLE_DONATION_DETAILS', 'donation_details'),

        'donation_programs' => env('DISTRIBUTION_FINANCING_TABLE_DONATION_PROGRAMS', 'programs'),

        'donation_funding_types' => env('DISTRIBUTION_FINANCING_TABLE_DONATION_FUNDING_TYPES', 'funding_types'),

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

        'donation_detail' => \Inisiatif\Distribution\Financings\Models\DonationDetail::class,

        'donation_program' => \Inisiatif\Distribution\Financings\Models\DonationProgram::class,

        'donation_funding_type' => \Inisiatif\Distribution\Financings\Models\DonationFundingType::class,

        'distribution' => \Inisiatif\Distribution\Financings\Models\Distribution::class,

        'program' => \Inisiatif\Distribution\Financings\Models\Program::class,

        'program_sector' => \Inisiatif\Distribution\Financings\Models\ProgramSector::class,
    ],
];
