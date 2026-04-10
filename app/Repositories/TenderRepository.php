<?php

namespace App\Repositories;

use App\Models\{Tender, Category, Region, Source};

class TenderRepository
{
    public function getFiltered($params, $perPage = 10)
    {
        $query = Tender::with(['category', 'region', 'source']);
        $query->when(!empty($params['search']), function ($q) use ($params) {
            $search = $params['search'];
            $q->where(function ($sub) use ($search) {
                $sub->where('title', 'like', '%' . $search . '%');
            });
        });

        $query->when(!empty($params['categoryId']), fn($q) => $q->whereIn('categoryId', (array)$params['categoryId']));
        $query->when(!empty($params['regionId']), fn($q) =>  $q->whereIn('regionId', (array)$params['regionId']));
        $query->when(!empty($params['sourceId']), fn($q) =>  $q->whereIn('sourceId', (array)$params['sourceId']));

        $query->when(isset($params['minBudget']), fn($q) => $q->where('budget', '>=', (float)$params['minBudget']));
        $query->when(isset($params['maxBudget']), fn($q) => $q->where('budget', '<=', (float)$params['maxBudget']));

        $query->when(!empty($params['closingDate']), fn($q) => $q->whereDate('deadline', $params['closingDate']));

        return $query->latest()->paginate($perPage);
    }

    public function getMetaData()
    {
        return [
            'categories' => Category::all(),
            'regions'    => Region::all(),
            'sources'    => Source::all(),
            'budgets'    => [
                'minBudget' => (float) Tender::min('budget') ?: 0,
                'maxBudget' => (float) Tender::max('budget') ?: 0,
            ],
            'deadlines'  => Tender::whereNotNull('deadline')->distinct()->pluck('deadline')
        ];
    }

    public function findById($id)
    {
        return Tender::with(['category', 'region', 'source'])->find($id);
    }

    public function create($data)
    {
        return Tender::create($data)->load(['category', 'region', 'source']);
    }
}
