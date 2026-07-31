<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{MasterController,EventController,AgendaPdfController,SettingsController};
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', fn () => redirect()->route('login'));
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest')->name('login.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
Route::get('/dashboard', [EventController::class,'dashboard'])->name('dashboard');
Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
Route::prefix('masters/{type}')->where(['type'=>'games|programs'])->group(function(){
 Route::get('/',[MasterController::class,'index'])->name('masters.index'); Route::get('/create',[MasterController::class,'create'])->name('masters.create'); Route::post('/',[MasterController::class,'store'])->name('masters.store'); Route::get('/{id}/edit',[MasterController::class,'edit'])->name('masters.edit'); Route::put('/{id}',[MasterController::class,'update'])->name('masters.update'); Route::delete('/{id}',[MasterController::class,'destroy'])->name('masters.destroy');
});
Route::resource('events',EventController::class)->except(['edit','update','destroy']);
Route::post('events/{event}/agenda',[EventController::class,'addAgenda'])->name('events.agenda.add'); Route::delete('events/{event}/agenda/{agendaItem}',[EventController::class,'removeAgenda'])->name('events.agenda.remove'); Route::post('events/{event}/budget',[EventController::class,'saveBudget'])->name('events.budget'); Route::post('events/{event}/expenses',[EventController::class,'addExpense'])->name('events.expenses'); Route::post('events/{event}/verify',[EventController::class,'verify'])->name('events.verify'); Route::post('events/{event}/ratings',[EventController::class,'rate'])->name('events.rate'); Route::get('events/{event}/pdf/{kind?}',AgendaPdfController::class)->where('kind','guest|internal')->name('events.pdf');
});
