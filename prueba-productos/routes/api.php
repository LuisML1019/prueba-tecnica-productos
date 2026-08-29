<?php

use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

Route::get('/productos', [ProductoController::class, 'index']);
Route::post('/productos/preview', [ProductoController::class, 'preview']);
Route::post('/productos/confirmar', [ProductoController::class, 'confirmar']);
Route::put('/productos/{producto}', [ProductoController::class, 'update']);