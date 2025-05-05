<?php

use App\Http\Controllers\API\TasksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::apiResource('tasks', TasksController::class)->except(['show']);
    Route::get('/tasks/trashed', [TasksController::class, 'trashed'])->name('tasks.trashed');
    Route::get('/tasks/completed', [TasksController::class, 'completed'])->name('tasks.completed');
    Route::get('/tasks/pending', [TasksController::class, 'pending'])->name('tasks.pending');
    Route::post('/tasks/{task}/complete', [TasksController::class, 'complete'])->name('tasks.complete');
}); 