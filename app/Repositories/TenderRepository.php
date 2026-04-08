<?php

namespace App\Repositories;

use App\Models\Tender;
use App\Models\Category;
use App\Models\Region;
use App\Models\Source;

class TenderRepository
{
    public function getPaginated($perPage = 10)
    {
        return Tender::with(['category', 'region', 'source'])
            ->latest()
            ->paginate($perPage);
    }

    public function filterTenders($data)
    {
        $query = Tender::with(['category', 'region', 'source']);

        $query->when(!empty($data['category_id'] ?? null), fn($q) => $q->whereIn('category_id', (array)$data['category_id']));
        $query->when(!empty($data['region_id'] ?? null), fn($q) => $q->whereIn('region_id', (array)$data['region_id']));
        $query->when(isset($data['min_budget']), fn($q) => $q->where('budget', '>=', (float)$data['min_budget']));
        $query->when(isset($data['max_budget']), fn($q) => $q->where('budget', '<=', (float)$data['max_budget']));
        $query->when(
            ($data['closingDate'] ?? null),
            fn($q) => $q->where('deadline', $data['closingDate'])
        );

        return $query->latest()->get();
    }

    public function getMetaData()
    {
        return [
            'categories' => Category::all(),
            'regions'    => Region::all(),
            'sources'    => Source::all(),
            'budgets'    => [
                'min_budget' => (float) Tender::min('budget') ?: 0,
                'max_budget' => (float) Tender::max('budget') ?: 0,
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
        $tender = Tender::create($data);
        return $tender->load(['category', 'region', 'source']);
    }

    public function search($term, $perPage = 10)
    {
        return Tender::with(['category', 'region', 'source'])
            ->where('title', 'like', '%' . $term . '%')
            ->latest()
            ->paginate($perPage);
    }
}
