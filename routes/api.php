<?php

use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\json;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['namespace' => 'App\Http\Controllers\API'], function () {

Route::post('admin/logout-all', 'AuthenticationController@logoutAll');
Route::post('login', 'AuthenticationController@login')->name('login');

Route::middleware('auth:sanctum')->group(function () {
Route::get('tickets/byid/{ticket}',[TicketController::class, 'show']);
Route::get('tickets/bystate/{state}', [TicketController::class, 'getByState']);
Route::post('tickets/reopen/{ticket}', [TicketController::class, 'reopen']);

Route::get('tickets',[TicketController::class, 'index']);
Route::post('tickets',[TicketController::class, 'store']);
});
});




