<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\Task\TaskController;

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
Route::get('validate_token',function (){
    return ['message'=> 'true'];
})->middleware('auth:sanctum');

Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);

/*
 * api endpoint with auth
 */
Route::group(['prefix' => 'task'],function (){

    Route::group(['middleware' =>'auth:sanctum'],function (){
        Route::get('all_task',[TaskController::class,'getTaskWithPagination']);
        Route::get('get_all_task',[TaskController::class,'getAllTask']);
        Route::get('get_task/{id}',[TaskController::class,'getTask']);
        Route::get('get_task_by_id/{id}',[TaskController::class,'getTaskGreaterThenId']);

        Route::post('add_task',[TaskController::class,'store']);
        Route::post('update_task',[TaskController::class,'update']);
        Route::post('delete_task',[TaskController::class,'destroy']);
    });
});

/*
 * api endpoint without auth
 */
Route::group(['prefix' => 'no_auth'],function (){

    Route::get('all_task',[TaskController::class,'getTaskWithPagination']);
    Route::get('get_all_task',[TaskController::class,'getAllTask']);
    Route::get('get_task/{id}',[TaskController::class,'getTask']);
    Route::get('get_task_by_id/{id}',[TaskController::class,'getTaskGreaterThenId']);
    Route::get('search/{query}',[TaskController::class,'searchTask']);

    Route::post('add_task',[TaskController::class,'store']);
    Route::post('update_task',[TaskController::class,'update']);
    Route::post('delete_task',[TaskController::class,'destroy']);

});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
