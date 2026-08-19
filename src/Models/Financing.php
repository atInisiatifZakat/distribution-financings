<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Inisiatif\Package\Common\Concerns\UuidPrimaryKey;
use Inisiatif\Package\Contract\Common\Model\ResourceInterface;

final class Financing extends Model implements ResourceInterface
{
    use UuidPrimaryKey;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'float',
        'distribution_at' => 'datetime',
    ];

    public function getConnectionName(): string
    {
        return \config('financing.connection', parent::getConnectionName());
    }

    public function getTable(): string
    {
        return \config('financing.table.distribution.financing', parent::getTable());
    }

    public function getId(): ?string
    {
        return $this->getAttribute('id');
    }

    public function setId($id): void
    {
        $this->setAttribute('id', $id);
    }

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(config('financing.models.distribution.model', Distribution::class));
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(config('financing.models.donation.model', Donation::class));
    }

    public function funding_type(): BelongsTo
    {
        return $this->belongsTo(
            config('financing.models.donation.funding_type', DonationFundingType::class),
            'donation_detail_funding_type_id'
        )->withTrashed();
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(
            config('financing.models.donation.program', DonationProgram::class),
            'donation_detail_program_id'
        )->withTrashed();
    }
}
