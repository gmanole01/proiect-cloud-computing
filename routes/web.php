<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\MainController;
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

Route::middleware('noauth')->group(function() {
    Route::get('/login', [AuthController::class, 'login_view'])->name('login');
    Route::post('/login', [AuthController::class, 'login_submit']);

    Route::get('/register', [AuthController::class, 'register_view'])->name('register');
    Route::post('/register', [AuthController::class, 'register_submit']);
});

Route::middleware('auth')->group(function() {
    Route::get('/', [MainController::class, 'home'])->name('home');
    Route::get('/add_image', [MainController::class, 'add_image'])->name('add_image');
    Route::post('/add_image', [MainController::class, 'add_image_submit']);
    Route::get('/image/{image}', [MainController::class, 'get_image']);
});
