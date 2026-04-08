<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TenderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Region;
use App\Models\Source;
use App\Models\Category;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::get('/tenders', [TenderController::class, 'index'])->name('tenders.index');
Route::get('/tenders/search', [TenderController::class, 'search'])->name('tenders.search');
Route::get('/tenders/filter', [TenderController::class, 'filter'])->name('tenders.filter');
Route::get('/tenders/{id}', [TenderController::class, 'show'])->name('tenders.show');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::put('/user/update', [AuthController::class, 'update']);
    Route::delete('/user/delete', [AuthController::class, 'delete']);

    Route::get('/regions', fn() => response()->json(Region::all()));
    Route::get('/sources', fn() => response()->json(Source::all()));
    Route::get('/categories', fn() => response()->json(Category::all()));

    Route::post('/tenders', [TenderController::class, 'store'])->name('tenders.store');

    Route::post('/tender/{id}/favorite', [TenderController::class, 'toggleFavorite'])->name('tenders.favorite.toggle');
    Route::get('/favorites', [TenderController::class, 'getFavorite'])->name('tenders.favorite.list');
});
