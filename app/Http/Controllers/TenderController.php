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
        
        // Modelda $with borligi uchun nomlar avtomatik chiqadi
        $tenders = Tender::latest()->paginate($perPage);
        
        return response()->json($tenders);
    }

    /**
     * Tenderlarni ID bo'yicha filter qilish
     */
    public function filter(TenderFilterRequest $request)
    {
        $query = Tender::query();

        // Ustozingiz aytganidek ID bilan qidiramiz (Tez va aniq)
        $query->when($request->category_id, fn($q, $v) => $q->whereIn('category_id', (array)$v));
        $query->when($request->region_id, fn($q, $v) => $q->whereIn('region_id', (array)$v));
        $query->when($request->source_id, fn($q, $v) => $q->whereIn('source_id', (array)$v));

        $query->when($request->min_budget, fn($q, $v) => $q->where('budget', '>=', $v));
        $query->when($request->max_budget, fn($q, $v) => $q->where('budget', '<=', $v));
        $query->when($request->closingDate, fn($q, $v) => $q->whereDate('deadline', $v));

        // Natijani olish (Model $with tufayli nomlarni qo'shib beradi)
        $tenders = $query->latest()->get();

        return response()->json($tenders);
    }

    /**
     * Yangi tender yaratish
     */
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

    // 1. Tenderni yaratamiz
    $tender = Tender::create($request->all());

    // 2. MUHIM: Yaratilgan tenderga bog'liq nomlarni yuklaymiz
    // Bu qator javobda category_id: 1 emas, balki category: {id: 1, name: "Technology"} chiqishini ta'minlaydi
    $tender->load(['category', 'region', 'source']);

    return response()->json([
        'message' => 'Tender muvaffaqiyatli yaratildi', 
        'data' => $tender 
    ], 201);
}

    /**
     * Bittadan tender ma'lumotini ko'rish
     */
    public function show($id)
    {
        $tender = Tender::find($id);
        
        if (!$tender) {
            return response()->json(['message' => 'Tender topilmadi'], 404);
        }
        
        return response()->json($tender);
    }

    /**
     * Tender ma'lumotlarini yangilash
     */
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
            'data'    => $tender->fresh()
        ]);
    }

    /**
     * Tenderni o'chirish
     */
    public function destroy($id)
    {
        $tender = Tender::find($id);
        
        if (!$tender) {
            return response()->json(['message' => 'Tender topilmadi'], 404);
        }
        
        $tender->delete();
        
        return response()->json(['message' => 'Tender muvaffaqiyatli o‘chirildi']);
    }

    /**
     * Qidiruv (Title bo'yicha)
     */
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

    public function getFavorite(Request $request)
    {
        return response()->json($request->user()->favorites);
    }
}