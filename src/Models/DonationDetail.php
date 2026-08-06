<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Inisiatif\Package\Contract\Common\Model\ResourceInterface;

final class DonationDetail extends Model implements ResourceInterface
{
    use SoftDeletes;

    protected $guarded = [];

    public function getConnectionName(): string
    {
        return \config('financing.connection', parent::getConnectionName());
    }

    public function getTable(): string
    {
        return \config('financing.table.donation.detail', parent::getTable());
    }

    public function getId(): ?string
    {
        $id = $this->getAttribute('id');

        return $id !== null ? (string) $id : null;
    }

    public function setId($id): void
    {
        $this->setAttribute('id', $id);
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(config('financing.models.donation.model', Donation::class));
    }

    public function funding(): BelongsTo
    {
        return $this->belongsTo(
            config('financing.models.donation.funding_type', DonationFundingType::class),
            'funding_type_id'
        )->withTrashed();
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(
            config('financing.models.donation.program', DonationProgram::class),
            'program_id'
        )->withTrashed();
    }
}
