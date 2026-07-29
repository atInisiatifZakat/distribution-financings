<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings;

use FromHome\ModelUpload\ModelUpload;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelPackageTools\Package;
use Inisiatif\ModelShared\ModelShared;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Inisiatif\Distribution\Financings\Models\Financing;
use Inisiatif\Distribution\Financings\ModelUploads\FinancingProcessor;

final class DistributionFinancingServiceProvider extends PackageServiceProvider
{
    public function bootingPackage(): void
    {
        ModelUpload::registerRecordProcessors([
            Financing::class => FinancingProcessor::class,
        ]);

        ModelShared::getDonationDetailModel()::resolveRelationUsing('program', function (Model $model) {
            return $model->belongsTo(ModelShared::getProgramModel()::class, 'program_id')
                ->withoutGlobalScopes();
        });
    }

    public function configurePackage(Package $package): void
    {
        $package->name('distribution-financings')
            ->hasConfigFile('financing')
            ->hasRoute('api')
            ->hasMigration('create_distribution_financings_table');
    }

    public function registeringPackage(): void
    {
        if ($this->app->runningUnitTests() && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom('../../migrations');
            $this->loadMigrationsFrom('../../migrations/testing');
        }
    }
}
