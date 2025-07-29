<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\DashboardComponent;
use App\Http\Livewire\QuerieComponent;
use App\Http\Livewire\LiveComponent;
use App\Http\Livewire\SettingComponent;

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
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', DashboardComponent::class)->name('dashboard');
    Route::get('/queries', QuerieComponent::class)->name('queries');
    Route::get('/live', LiveComponent::class)->name('live');
    Route::get('/settings', SettingComponent::class)->name('settings');
});
