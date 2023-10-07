<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TodoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});

Auth::routes();

Route::controller(TodoController::class)->group(function(){

    Route::get('/task/list', 'index');

    Route::get('/task/create/{id?}', 'showTaskForm');
    Route::post('/task/create/{id?}', 'createTask');

    Route::get('/task/show/{id?}', 'showTask');

    Route::get('/task/edit/{id}', 'editTask');
    Route::post('/task/update/{id}', 'editTask');

    Route::post('/task/complete', 'completeTask');

    Route::post('/task/delete', 'deleteTask');

    Route::get('/user/apiKey', 'apiKey')->name('apiKey');
    Route::post('/user/apiKey/generate', 'generateApiKey');
});
