<?php

namespace Daynnnnn\StatamicDatabase\Taxonomies;

use Statamic\Stache\Repositories\TermRepository as StacheRepository;
use Statamic\Contracts\Taxonomies\Term as TermContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Statamic\Taxonomies\TermCollection;

class TermRepository extends StacheRepository
{
    public static function bindings(): array
    {
        return [
            TermContract::class => Term::class,
        ];
    }

    public function all(): TermCollection
    {
        $keys = TermModel::get()->map(function ($model) {
            return Term::fromModel($model);
        });

        return TermCollection::make($keys);
    }

    public function find($id): TermContract {
        // TODO: Less hacky way of converting refference to id, possibly
        //       able to replace refference function altogether?
        if (($model = TermModel::where('slug', Str::afterLast($id, '::'))->first()) == null) {
            return null;
        }

        $term = Term::fromModel($model);
        return $term;
    }

    public function findByHandle($handle): ?Term
    {
        return $this->find($handle);
    }

    public function save($term)
    {
        $model = $term->toModel();

        $model->save();

        $term->model($model->fresh());
    }

    public function delete($term)
    {
        $term->toModel()->delete();
    }

    public function query()
    {
        $this->ensureAssociations();

        return new TermQueryBuilder(TermModel::query());
    }
}
