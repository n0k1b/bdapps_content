<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AppsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/',[AdminController::class,'show_dashboard']);





//Route::get('logout_admin','AdminController@logout')->name('logout_admin');




//Report Start
Route::get('report/{type}',[AdminController::class,'report_view']);

Route::get('show_order_report',[ReportController::class,'show_order_report'])->name('show_order_report');

//




//apps start
Route::get('show-all-apps',[AppsController::class,'show_all_apps'])->name('show-all-apps');
Route::get('add-apps',[AppsController::class,'add_apps_ui'])->name('add-apps');
Route::post('add-apps',[AppsController::class,'add_apps'])->name('add-apps');
Route::get('edit_apps_content/{id}',[AppsController::class,'edit_apps_content_ui'])->name('edit_apps_content');
Route::post('update_apps_content',[AppsController::class,'update_apps_content'])->name('update_apps_content');
Route::get('apps_content_delete/{id}',[AppsController::class,'apps_content_delete'])->name('apps_content_delete');

//apps end



