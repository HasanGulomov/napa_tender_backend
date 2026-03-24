<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TenderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);
Route::get('/tenders', [TenderController::class, 'index']); 

Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/tender/{id}/favorite', [TenderController::class, 'toggleFavorite']);
    
    Route::get('/favorites', [TenderController::class, 'getFavorite']);
    
    Route::apiResource('tenders', TenderController::class)->except(['index', 'show']);
});