<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\TenderFilterRequest;

class TenderController extends Controller
{
    
    public function index(Request $request)
    {
        
        $perPage = $request->query('per_page', 10);
        
        $tenders = Tender::latest()->paginate($perPage);
        
        return response()->json($tenders);
    }

    
    public function search(Request $request)
    {
        $request->validate(['search' => 'required|string']);
        
        $perPage = $request->query('per_page', 10);
        
        $tenders = Tender::where('title', 'like', '%' . $request->search . '%')
            ->latest()
            ->paginate($perPage)
            ->appends($request->query()); 

        return response()->json($tenders);
    }

    
  public function filter(TenderFilterRequest $request) 
{
    $query = Tender::query(); 

    $query->when($request->category, function($q, $v) {
        $categories = is_array($v) ? $v : array_map('trim', explode(',', $v));
        $q->whereIn('category', $categories);
    });

  
    $query->when($request->region, function ($q, $v) {
        $regions = is_array($v) ? $v : array_map('trim', explode(',', $v));
        $q->where(function ($subQuery) use ($regions) {
            foreach ($regions as $region) {
                $subQuery->orWhere('location', 'like', "%$region%");
            }
        });
    });

    $query->when($request->source, function($q, $v) {
        $sources = is_array($v) ? $v : array_map('trim', explode(',', $v));
        $q->whereIn('source', $sources);
    });

    $query->when($request->min_budget, function($q, $v) {
        $q->whereRaw('CAST(budget AS UNSIGNED) >= ?', [(int)$v]);
    });

    $query->when($request->max_budget, function($q, $v) {
        $q->whereRaw('CAST(budget AS UNSIGNED) <= ?', [(int)$v]);
    });

    $query->when($request->closingDate, fn($q, $v) => $q->whereDate('deadline', $v));

    $query->latest();

    return response()->json($query->get());
}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'location' => 'required|string',
            'deadline' => 'required|date',
            'budget' => 'required|numeric',
            'source' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tender = Tender::create($request->all());
        return response()->json(['message' => 'Tender muvaffaqiyatli yaratildi', 'data' => $tender], 201);
    }

    public function show($id)
    {
        $tender = Tender::find($id);
        if (!$tender) return response()->json(['message' => 'Tender topilmadi'], 404);
        return response()->json($tender);
    }

    public function update(Request $request, $id)
    {
        $tender = Tender::find($id);
        if (!$tender) return response()->json(['message' => 'Tender topilmadi'], 404);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => 'sometimes|string',
            'location' => 'sometimes|string',
            'deadline' => 'sometimes|date',
            'budget' => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tender->update($request->all());
        return response()->json(['message' => 'Tender yangilandi', 'data' => $tender]);
    }

    public function destroy($id)
    {
        $tender = Tender::find($id);
        if (!$tender) return response()->json(['message' => 'Tender topilmadi'], 404);
        
        $tender->delete();
        return response()->json(['message' => 'Tender muvaffaqiyatli o‘chirildi']);
    }

    public function toggleFavorite(Request $request, $id)
    {
        $user = $request->user();
        $tender = Tender::findOrFail($id);
        $user->favorites()->toggle($tender->id);

        return response()->json([   
            'message' => 'Muvaffaqiyatli bajarildi',
            'is_favorite' => $user->favorites()->where('tender_id', $id)->exists()
        ]);
    }

    public function getFavorite(Request $request)
    {
        return response()->json($request->user()->favorites);
    }
}