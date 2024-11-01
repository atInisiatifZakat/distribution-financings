<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Inisiatif\Package\Contract\Common\Model\ResourceInterface;

final class Program extends Model implements ResourceInterface
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getId(): ?string
    {
        return $this->getAttribute('id');
    }

    public function setId($id): void
    {
        $this->setAttribute('id', $id);
    }
}
