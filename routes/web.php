<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{MasterController,EventController,AgendaPdfController,SettingsController,ContributorController,CollectionController,NotificationController};
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
Route::post('/settings/test-mail',[SettingsController::class,'testMail'])->name('settings.mail.test');
Route::get('/contributors',[ContributorController::class,'index'])->name('contributors.index'); Route::post('/contributors',[ContributorController::class,'store'])->name('contributors.store'); Route::put('/contributors/{contributor}',[ContributorController::class,'update'])->name('contributors.update'); Route::patch('/contributors/{contributor}/toggle',[ContributorController::class,'toggle'])->name('contributors.toggle');
Route::prefix('masters/{type}')->where(['type'=>'games|programs'])->group(function(){
 Route::get('/',[MasterController::class,'index'])->name('masters.index'); Route::get('/create',[MasterController::class,'create'])->name('masters.create'); Route::post('/',[MasterController::class,'store'])->name('masters.store'); Route::get('/{id}/edit',[MasterController::class,'edit'])->name('masters.edit'); Route::put('/{id}',[MasterController::class,'update'])->name('masters.update'); Route::delete('/{id}',[MasterController::class,'destroy'])->name('masters.destroy');
});
Route::resource('events',EventController::class)->except(['edit','update','destroy']);
Route::post('events/{event}/agenda',[EventController::class,'addAgenda'])->name('events.agenda.add'); Route::delete('events/{event}/agenda/{agendaItem}',[EventController::class,'removeAgenda'])->name('events.agenda.remove'); Route::post('events/{event}/budget',[EventController::class,'saveBudget'])->name('events.budget'); Route::post('events/{event}/expenses',[EventController::class,'addExpense'])->name('events.expenses'); Route::post('events/{event}/verify',[EventController::class,'verify'])->name('events.verify'); Route::post('events/{event}/ratings',[EventController::class,'rate'])->name('events.rate'); Route::get('events/{event}/pdf/{kind?}',AgendaPdfController::class)->where('kind','guest|internal')->name('events.pdf');
Route::get('events/{event}/agenda/{agendaItem}/edit',[EventController::class,'editAgenda'])->name('events.agenda.edit'); Route::put('events/{event}/agenda/{agendaItem}',[EventController::class,'updateAgenda'])->name('events.agenda.update');
Route::get('events/{event}/collection',[CollectionController::class,'show'])->name('events.collection'); Route::patch('events/{event}/collection/per-head',[CollectionController::class,'setPerHead'])->name('events.collection.per-head'); Route::post('events/{event}/collection',[CollectionController::class,'add'])->name('events.collection.add'); Route::patch('events/{event}/collection/{contribution}/payment',[CollectionController::class,'payment'])->name('events.collection.payment'); Route::delete('events/{event}/collection/{contribution}',[CollectionController::class,'remove'])->name('events.collection.remove'); Route::get('events/{event}/collection/pdf',[CollectionController::class,'pdf'])->name('events.collection.pdf');
Route::get('events/{event}/notifications',[NotificationController::class,'show'])->name('events.notifications'); Route::post('events/{event}/notifications',[NotificationController::class,'send'])->name('events.notifications.send');
});
