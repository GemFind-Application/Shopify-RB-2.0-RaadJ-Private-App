<?php



use App\Http\Controllers\AppProxyController;

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

Route::any('/engagement-rings', 'App\Http\Controllers\AppProxyController@index')->name('engagement-rings');

Route::any('/engagement-rings/settings', 'App\Http\Controllers\AppProxyController@index')->name('engagement-rings');

Route::any('/engagement-rings/diamonds', 'App\Http\Controllers\AppProxyController@index')->name('engagement-rings');

Route::any('/engagement-rings/navlabgrown', 'App\Http\Controllers\AppProxyController@index')->name('engagement-rings');

Route::any('/engagement-rings/compare', 'App\Http\Controllers\AppProxyController@index')->name('engagement-rings');

Route::any('/engagement-rings/navfancycolored', 'App\Http\Controllers\AppProxyController@index')->name('engagement-rings');

Route::any('/engagement-rings/settings/{any}', 'App\Http\Controllers\AppProxyController@index')->where('any', '.*');

Route::any('/engagement-rings/diamonds/product/{any}', 'App\Http\Controllers\AppProxyController@index');

Route::any('/engagement-rings/completering', 'App\Http\Controllers\AppProxyController@index')->name('engagement-rings');

Route::any('/engagement-rings/labgrownsettings', 'App\Http\Controllers\AppProxyController@index')->name('engagement-rings');

Route::any('/engagement-rings/labgrownsettings/{any}', 'App\Http\Controllers\AppProxyController@index')->where('any', '.*');









Route::get('/', function () {

    return view('welcome');

})->middleware(['verify.shopify'])->name('home');



Route::get('/{path?}', function () {

    return view('welcome');

})->middleware(['verify.shopify'])->name('home');


Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    // return what you want
});