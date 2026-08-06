<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Inisiatif\Package\Contract\Common\Model\ResourceInterface;

final class DonationFundingType extends Model implements ResourceInterface
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'id' => 'int',
        'is_terikat' => 'bool',
        'is_dskl' => 'bool',
        'print_bsz' => 'bool',
    ];

    public function getConnectionName(): string
    {
        return \config('financing.connection', parent::getConnectionName());
    }

    public function getTable(): string
    {
        return \config('financing.table.donation.funding_type', parent::getTable());
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
}
