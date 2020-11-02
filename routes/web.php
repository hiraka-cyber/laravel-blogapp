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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::prefix('admin')->group(function() {
    Route::get('form/{article_id?}', 'App\Http\Controllers\AdminBlogController@form')->name('admin_form');
    Route::post('post', 'App\Http\Controllers\AdminBlogController@post')->name('admin_post');
    Route::post('delete', 'App\Http\Controllers\AdminBlogController@delete')->name('admin_delete');
    Route::get('list', 'App\Http\Controllers\AdminBlogController@list')->name('admin_list');

    Route::get('category', 'App\Http\Controllers\AdminBlogController@category')->name('admin_category');
    Route::post('category/edit', 'App\Http\Controllers\AdminBlogController@editCategory')->name('admin_category_edit');
    Route::post('category/delete', 'App\Http\Controllers\AdminBlogController@deleteCategory')->name('admin_category_delete');
});

Route::get('/', 'App\Http\Controllers\AdminBlogController@list')->name('admin_list');