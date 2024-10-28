<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Inisiatif\Distribution\Financings\DistributionFinancingServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            DistributionFinancingServiceProvider::class,
        ];
    }
}
