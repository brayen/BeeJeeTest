<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\TodoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post(    'createTask', [TodoController::class, 'create']);
Route::get(     'getTasks',   [TodoController::class, 'read']);

Route::put(     'updateTask',   [TodoController::class, 'update']);
Route::put(     'completeTask', [TodoController::class, 'complete']);

Route::delete(  'deleteTask', [TodoController::class, 'delete']);

