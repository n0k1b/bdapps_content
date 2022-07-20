<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\AppList;
use App\Http\Controllers\AppsController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::get('ussd/{slug?}', function ($slug) {
//     $app_name = AppList::where('app_id',$slug)->first();
//     file_put_contents('test.txt',$app_name);
// });
Route::get('ussd/{id}',[AppsController::class,'ussd']);