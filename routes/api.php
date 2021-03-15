<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/job', 'TaskController@storeTask'); //Create Task
Route::get('/job', 'TaskController@nextTask'); //Get the next task to process by priority
Route::get('/job/{taskId}', 'TaskController@getTask'); //Get status of a Task by ID
Route::put('/job/{taskId}', 'TaskController@updateTask'); //Update queued Task
