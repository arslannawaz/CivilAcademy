<?php

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



Route::get('/error', function () {
    return response()->json(['error' => 'Unauthorized'], 401);
})->name('error');


Route::get('/charge', 'PaypalController@charge');
Route::get('paymentsuccess', 'PaypalController@payment_success');
Route::get('paymenterror', 'PaypalController@payment_error');

Route::get('/charge-offer/{id}', 'PaypalOfferController@charge');
Route::get('payment-success', 'PaypalOfferController@payment_success');
Route::get('payment-error', 'PaypalOfferController@payment_error');
