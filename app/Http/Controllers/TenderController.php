<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;

class TenderController extends Controller
{
    public function index()
    {
        return response()->json(Tender::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string',
            'status_percentage' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string',
            'budget' => 'required|string',
            'location' => 'required|string',
            'deadline' => 'required|date',
        ]);

        $tender = Tender::create($data);
        return response()->json($tender, 201);
    }

    public function show($id)
    {
        $tender = Tender::find($id);
        if (!$tender) return response()->json(['message' => 'Topilmadi'], 404);
        return response()->json($tender);
    }

    public function update(Request $request, $id)
    {
        $tender = Tender::find($id);
        if (!$tender) return response()->json(['message' => 'Topilmadi'], 404);

        $tender->update($request->all());
        return response()->json($tender);
    }

    public function destroy($id)
    {
        $tender = Tender::find($id);
        if (!$tender) return response()->json(['message' => 'Topilmadi'], 404);
        
        $tender->delete();
        return response()->json(["message" => "O'chirildi"]);
    }

    public function toggleFavorite(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Avtorizatsiyadan oting'], 401);

        $tender = Tender::findOrFail($id);
        $user->favorites()->toggle($tender->id);

        return response()->json([   
            'message' => 'Muvaffaqiyatli bajarildi',
            'is_favorite' => $user->favorites()->where('tender_id', $id)->exists()
        ]);
    }

    public function getFavorite(Request $request)
    {
        if (!$request->user()) return response()->json(['message' => 'Avtorizatsiyadan oting'], 401);
        return response()->json($request->user()->favorites);
    }
}