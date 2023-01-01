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

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@home')->name('home');
Route::get('/ongoing', 'HomeController@ongoing')->name('ongoing');
Route::get('/completed', 'HomeController@completed')->name('completed');
Route::get('/updated', 'HomeController@updated')->name('updated');

// id = anime id encode to string
Route::get('/{id}-f{slug}', 'AnimeController@anime')->name('anime')->where('id', '[a-zA-Z0-9]+')->where('slug', '[a-zA-Z0-9-]+');


