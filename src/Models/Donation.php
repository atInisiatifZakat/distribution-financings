<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Models;

use Illuminate\Database\Eloquent\Model;
use Inisiatif\Package\User\ModelRegistrar;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Inisiatif\Package\Common\Concerns\UuidPrimaryKey;
use Inisiatif\ModelShared\Registrars\DonorModelRegistrar;
use Inisiatif\Package\Contract\Common\Model\ResourceInterface;

final class Donation extends Model implements ResourceInterface
{
    use UuidPrimaryKey;

    protected $guarded = [];

    protected $appends = ['amount_remaining'];

    protected $casts = [
        'amount' => 'float',
    ];

    public function getConnectionName(): string
    {
        return \config('financing.connection', parent::getConnectionName());
    }

    public function getTable(): string
    {
        return \config('financing.table.donations', parent::getTable());
    }

    public function getId(): ?string
    {
        return $this->getAttribute('id');
    }

    public function setId($id): void
    {
        $this->setAttribute('id', $id);
    }

    public function financing(): HasMany
    {
        return $this->hasMany(Financing::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(ModelRegistrar::getBranchModelClass());
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(ModelRegistrar::getEmployeeModelClass());
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(app(DonorModelRegistrar::class)->getModelClassName());
    }

    public function checkAmountRemaining(): int|float
    {
        $totalFinancingAmount = $this->getAttribute('financing')->sum('amount');

        return $this->getAttribute('total_amount') - $totalFinancingAmount;
    }

    public function amountRemaining(): Attribute
    {
        return new Attribute(
            get: fn () => $this->checkAmountRemaining(),
        );
    }
}
