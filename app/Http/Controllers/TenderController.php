<?php

namespace App\Http\Controllers;

use App\Http\Requests\{StoreTenderRequest, TenderFilterRequest};
use App\Http\Resources\TenderResource;
use App\Services\TenderService;
use Illuminate\Http\Request;

class TenderController extends Controller
{
    protected $service;

    public function __construct(TenderService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return TenderResource::collection($this->service->list($request->query('per_page', 10)));
    }

    public function filter(TenderFilterRequest $request)
    {
        return TenderResource::collection($this->service->filter($request->validated()));
    }

    public function getFilterData()
    {
        return response()->json($this->service->meta());
    }

    public function store(StoreTenderRequest $request)
    {
        $tender = $this->service->store($request->validated());
        return response()->json(['message' => 'Muvaffaqiyatli yaratildi', 'data' => new TenderResource($tender)], 201);
    }

    public function show($id)
    {
        $tender = $this->service->get($id);
        return $tender ? new TenderResource($tender) : response()->json(['message' => 'Topilmadi'], 404);
    }

    public function search(Request $request)
    {
        $request->validate(['search' => 'required|string']);
        return TenderResource::collection($this->service->search($request->search, $request->query('per_page', 10)));
    }

    public function toggleFavorite(Request $request, $id)
    {
        $attached = $this->service->toggleFavorite($request->user(), $id);
        if ($attached === null) return response()->json(['message' => 'Topilmadi'], 404);

        return response()->json([
            'message' => $attached ? 'Sevimlilarga qo‘shildi' : 'Olindi',
            'is_favorite' => $attached
        ]);
    }

    public function getFavorite(Request $request)
    {
        return TenderResource::collection($request->user()->favorites);
    }
}
