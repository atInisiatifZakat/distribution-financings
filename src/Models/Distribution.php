<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Inisiatif\Package\Common\Concerns\UuidPrimaryKey;
use Inisiatif\Package\Contract\Common\Model\ResourceInterface;

final class Distribution extends Model implements ResourceInterface
{
    use UuidPrimaryKey;

    public const DISTRIBUTION_AS_MONEY = 'MONEY';

    public const DISTRIBUTION_AS_ITEM = 'ITEM';

    public const DISTRIBUTION_AS_MONEY_AND_ITEM = 'MONEY_ITEM';

    protected $guarded = [];

    protected $casts = [
        'amount' => 'float',
        'amount_realization' => 'float',
        'request_at' => 'datetime',
        'percent_management_fee' => 'float',
        'amount_management_fee' => 'float',
        'distribution_at' => 'datetime',
        'last_create_timeline_at' => 'datetime',
        'last_create_team_at' => 'datetime',
        'last_create_beneficiary_at' => 'datetime',
    ];

    public function financing(): HasMany
    {
        return $this->hasMany(Financing::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(config('financing.model.program', Program::class));
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(config('financing.model.sector', ProgramSector::class));
    }

    public function getId(): ?string
    {
        return $this->getAttribute('id');
    }

    public function setId($id): void
    {
        $this->setAttribute('id', $id);
    }

    public function getFormattedStringDate(): string
    {
        /** @var Carbon $date */
        $date = $this->getAttribute('request_at');

        $date->locale('id');

        $date->settings([
            'formatFunction' => 'translatedFormat',
        ]);

        return $date->format('l, d F Y');
    }

    public function isOverRequestAmount(float|int $requestAmount): bool
    {
        return $requestAmount > $this->getAttribute('amount') ||
        $requestAmount < $this->getAttribute('amount');
    }
}
