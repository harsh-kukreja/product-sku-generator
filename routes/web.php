<?php

use App\Http\Controllers\BaseProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPermuteController;
use App\Http\Controllers\VariantTypeController;
use App\Models\ProductPermute;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Auth::routes();

Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
	Route::resource('user', 'App\Http\Controllers\UserController', ['except' => ['show']]);
	Route::get('profile', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\ProfileController@edit']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'App\Http\Controllers\ProfileController@update']);
	Route::get('upgrade', function () {return view('pages.upgrade');})->name('upgrade');
	 Route::get('map', function () {return view('pages.maps');})->name('map');
	 Route::get('icons', function () {return view('pages.icons');})->name('icons');
	 Route::get('table-list', function () {return view('pages.tables');})->name('table');
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);

    Route::get('/product/datatables', [BaseProductController::class, 'datatables'])->name('product.datatables');
    Route::get('/product/{id}/sku', [BaseProductController::class, 'productSku'])->name('product.sku');
    Route::resource('/product', BaseProductController::class)->only(['index', 'create', 'store']);

    Route::delete('/sku/{id}', [ProductPermuteController::class, 'destroy'])->name('product.sku.destroy');
    Route::get('/product/{id}/sku/datatables', [ProductPermuteController::class, 'datatables'])->name('product.sku.datatables');

});

