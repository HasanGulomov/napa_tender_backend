<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\TenderFilterRequest;
use App\Http\Resources\TenderResource;

class TenderController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $tenders = Tender::with(['category', 'region', 'source'])
            ->latest()
            ->paginate($perPage);


        return TenderResource::collection($tenders);
    }

    public function filter(TenderFilterRequest $request)
    {
        $query = Tender::with(['category', 'region', 'source']);

        $query->when(
            !empty($request->category_id),
            fn($q) =>
            $q->whereIn('category_id', (array)$request->category_id)
        );

        $query->when(
            !empty($request->region_id),
            fn($q) =>
            $q->whereIn('region_id', (array)$request->region_id)
        );

        $query->when(
            !empty($request->source_id),
            fn($q) =>
            $q->whereIn('source_id', (array)$request->source_id)
        );

        $query->when(
            $request->has('min_budget'),
            fn($q) =>
            $q->where('budget', '>=', (float)$request->min_budget)
        );

        $query->when(
            $request->has('max_budget'),
            fn($q) =>
            $q->where('budget', '<=', (float)$request->max_budget)
        );

        $query->when(
            $request->closingDate,
            fn($q) =>
            $q->whereDate('deadline', '<=', \Carbon\Carbon::parse($request->closingDate))
        );

        return TenderResource::collection($query->latest()->get());
    }


    public function getFilterData()
    {
        return response()->json([
            'budgets' => [
                'min_budget' => (float) Tender::min('budget') ?? 0,
                'max_budget' => (float) Tender::max('budget') ?? 0,
            ],
            'deadlines' => Tender::whereNotNull('deadline')->distinct()->pluck('deadline')
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            'region_id' => 'required|integer|exists:regions,id',
            'source_id' => 'required|integer|exists:sources,id',
            'deadline' => 'required|date',
            'budget' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tender = Tender::create($request->all());
        $tender->load(['category', 'region', 'source']);

        return response()->json([
            'message' => 'Tender muvaffaqiyatli yaratildi',
            'data' => new TenderResource($tender)
        ], 201);
    }

    public function show($id)
    {
        $tender = Tender::with(['category', 'region', 'source'])->find($id);

        if (!$tender) {
            return response()->json(['message' => 'Tender topilmadi'], 404);
        }

        return new TenderResource($tender);
    }

    public function update(Request $request, $id)
    {
        $tender = Tender::find($id);

        if (!$tender) {
            return response()->json(['message' => 'Tender topilmadi'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|integer|exists:categories,id',
            'region_id'   => 'sometimes|integer|exists:regions,id',
            'source_id'   => 'sometimes|integer|exists:sources,id',
            'deadline'    => 'sometimes|date',
            'budget'      => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tender->update($request->all());

        return response()->json([
            'message' => 'Tender yangilandi',
            'data'    => new TenderResource($tender->fresh())
        ]);
    }

    public function destroy($id)
    {
        $tender = Tender::find($id);

        if (!$tender) {
            return response()->json(['message' => 'Tender topilmadi'], 404);
        }

        $tender->delete();

        return response()->json(['message' => 'Tender muvaffaqiyatli o‘chirildi']);
    }

    public function search(Request $request)
    {
        $request->validate(['search' => 'required|string']);

        $perPage = $request->query('per_page', 10);

        $tenders = Tender::with(['category', 'region', 'source'])
            ->where('title', 'like', '%' . $request->search . '%')
            ->latest()
            ->paginate($perPage);

        return TenderResource::collection($tenders);
    }

    public function toggleFavorite(Request $request, $id)
    {
        $user = $request->user();
        $tender = Tender::find($id);

        if (!$tender) {
            return response()->json(['message' => 'Tender topilmadi'], 404);
        }

        $status = $user->favorites()->toggle($id);
        $attached = count($status['attached']) > 0;

        return response()->json([
            'message' => $attached ? 'Tender sevimlilarga qo‘shildi' : 'Tender sevimlilardan olib tashlandi',
            'is_favorite' => $attached
        ]);
    }

    public function getFavorite(Request $request)
    {

        return TenderResource::collection($request->user()->favorites);
    }
}
