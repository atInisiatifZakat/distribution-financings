<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Scopes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Inisiatif\ModelShared\Registrars\DonorModelRegistrar;

final class DonationSearchScope implements Scope
{
    /**
     * @psalm-suppress PossiblyInvalidMethodCall
     */
    public function apply(Builder $builder, Model $model): void
    {
        /** @var string|null $keyword */
        $keyword = request()?->query('q');

        if ($keyword !== null) {
            $builder->where(static function (Builder $builder) use ($keyword): Builder {
                $value = \mb_strtolower($keyword, 'UTF8');

                $grammar = $builder->getQuery()->getGrammar();

                $donorTable = app(DonorModelRegistrar::class)->getTableName();

                $name = $grammar->wrap($builder->qualifyColumn($donorTable.'.name'));
                $number = $grammar->wrap($builder->qualifyColumn('identification_number'));

                return $builder->orWhereRaw("LOWER({$number}) LIKE ?", ["%{$value}%"])
                    ->orWhereRaw("LOWER({$name}) LIKE ?", ["%{$value}%"]);
            });
        }
    }
}
