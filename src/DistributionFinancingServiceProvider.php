<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class DistributionFinancingServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('distribution-financings')
            ->hasConfigFile('financing');
    }
}
