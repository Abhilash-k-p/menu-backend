<?php

use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

Route::get('/menus', [MenuController::class, 'index']); // Get all menus
Route::post('/menus', [MenuController::class, 'store']); // Add a new menu item
Route::put('/menus/{id}', [MenuController::class, 'update']); // Update an existing menu item
Route::delete('/menus/{id}', [MenuController::class, 'destroy']); // Delete a menu item
