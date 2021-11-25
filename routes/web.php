<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LevelsController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\StreamsController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\UsersController;
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

Route::get('/', function(){
    return view('welcome');
})->name('welcome');

Route::group(['middleware' => ['auth']], function(){
    
    Route::get('/home', HomeController::class)->name('home');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('users', UsersController::class)
        ->only(['index']);

    Route::resource('levels',LevelsController::class)
           ->only(['index']);   
           
    Route::resource('streams',StreamsController::class)
           ->only(['index']);        
  
    Route::resource('teachers', TeachersController::class)
        ->only('index');

    Route::resource('departments',DepartmentsController::class)
         ->only('index');    

    Route::resource('subjects',SubjectsController::class)
    ->only('index');   
    
    Route::resource('roles',RolesController::class)
    ->only('index'); 
    
    Route::resource('permissions',PermissionsController::class)
    ->only('index'); 
});