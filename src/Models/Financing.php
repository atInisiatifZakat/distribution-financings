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
        return \config('financing.table.distribution_financings', parent::getTable());
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
        return $this->belongsTo(config('financing.model.distribution', Distribution::class));
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(config('financing.model.donation', Donation::class));
    }
}
