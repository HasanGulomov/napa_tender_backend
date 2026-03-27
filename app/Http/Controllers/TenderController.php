<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;

class TenderController extends Controller
{
    /**
     * Tenderlar ro'yxatini chiqarish va filtrlash (GET)
     */
    public function index(Request $request)
    {
        $query = Tender::query();

        // Filtrlash mantiqi
        $query->when($request->category, fn($q) => 
            $q->where('category', $request->category)
        )
        ->when($request->search, fn($q) => 
            $q->where('title', 'like', '%' . $request->search . '%')
        )
        ->when($request->location, fn($q) => 
            $q->where('location', 'like', '%' . $request->location . '%')
        )
        ->when($request->deadline, fn($q) => 
            $q->whereDate('deadline', '>=', $request->deadline)
        )
        ->when($request->min_budget, fn($q) => 
            $q->where('budget', '>=', $request->min_budget)
        )
        ->when($request->max_budget, fn($q) => 
            $q->where('budget', '<=', $request->max_budget)
        );

        // Yangilarini birinchi chiqarish va sahifalash
        $tenders = $query->latest()->paginate(10)->appends($request->query());

        return response()->json($tenders);
    }

   
    public function show($id)
    {
        $tender = Tender::find($id);
        
        if (!$tender) {
            return response()->json(['message' => 'Tender topilmadi'], 404);
        }

        return response()->json($tender);
    }

    
   
    public function toggleFavorite(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Avtorizatsiyadan oting'], 401);
        }

        $tender = Tender::findOrFail($id);
        
      
        $user->favorites()->toggle($tender->id);

        return response()->json([   
            'message' => 'Muvaffaqiyatli bajarildi',
            'is_favorite' => $user->favorites()->where('tender_id', $id)->exists()
        ]);
    }

   
    public function getFavorite(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Avtorizatsiyadan oting'], 401);
        }

        return response()->json($user->favorites);
    }
}