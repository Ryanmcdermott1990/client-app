<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginController;
use Illuminate\Auth\Events\Logout;

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

// Wrapped the routes protected by the sanctum middleware
// Routes include the get request that returns the view of the dashboard 
// Since the client app is a SPA, it is using Inertia adapter to intercept the request and render the Svelte 'Index' page
// The logout route has also been added 
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/', function () {
        return Inertia\Inertia::render('Index');
    })->name('dashboard');
    Route::get('/logout', [AuthController::class, 'logout']);
});

// Get request to get the login view, Intertia intercepts this request and render the 'Login' Svelte page  
Route::get('/login', function () {
    return Inertia\Inertia::render('Login');
})->name('login');

// The redirect request and callback for authorizing the user through the auth server
// Both of these middleware have been wrapped in the web middleware 
Route::group(['middleware' => ['web']], function () {
    Route::get('/redirect', [App\Http\Controllers\AuthController::class, 'redirect']);
    Route::get('/callback', [App\Http\Controllers\AuthController::class, 'callback']);
});
