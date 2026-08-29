<?php

use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

Route::get('/productos', [ProductoController::class, 'index']);
Route::post('/productos/importar', [ProductoController::class, 'importar']);
Route::put('/productos/{producto}', [ProductoController::class, 'update']);