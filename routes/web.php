<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\AuthController;


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

Route::group(['middleware'=> ['auth:sanctum']], function () {
Route::get('/', function() {
return Inertia\Inertia::render('Index');
    });
});

Route::post('/signup', [LoginController::class, 'sign_up']);

Route::get('/login', function() {
    return Inertia\Inertia::render('Login');
        })->name('login');

Route::get('/redirect', [App\Http\Controllers\AuthController::class, 'redirect']);
Route::get('/callback', [App\Http\Controllers\AuthController::class, 'callback']);
    
    