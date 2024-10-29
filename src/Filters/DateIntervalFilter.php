<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Filters;

use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Exceptions\InvalidFilterValue;

final class DateIntervalFilter implements Filter
{
    /**
     * @param  mixed  $value
     *
     * @throws InvalidFilterValue
     *
     * @psalm-suppress PossiblyFalseReference
     */
    public function __invoke(Builder $query, $value, string $property): void
    {
        if (\is_string($value)) {
            $date = Carbon::parse($value);

            $query->whereBetween($property, [
                $date->startOfDay(), $date->endOfDay(),
            ]);
        }

        if (\is_array($value) && \count($value) > 1) {
            $params = \array_slice($value, 0, 2);

            try {
                $query->whereBetween($property, [
                    Carbon::createFromFormat('Y-m-d', $params[0])->startOfDay(),
                    Carbon::createFromFormat('Y-m-d', $params[1])->endOfDay(),
                ]);
            } catch (\Throwable $exception) {
                throw InvalidFilterValue::make($value);
            }
        }
    }
}
