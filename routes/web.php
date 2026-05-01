<?php

use App\Http\Controllers\Admin\EntityContactController as AdminEntityContactController;
use App\Http\Controllers\Admin\EntityController as AdminEntityController;
use App\Http\Controllers\Admin\EntityDetailController as AdminEntityDetailController;
use App\Http\Controllers\Admin\EntityTranslationController as AdminEntityTranslationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'admin'])->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/entities', [AdminEntityController::class, 'index'])->name('entities.index');
    Route::get('/entities/create', [AdminEntityController::class, 'create'])->name('entities.create');
    Route::post('/entities', [AdminEntityController::class, 'store'])->name('entities.store');
    Route::get('/entities/{entity}/edit', [AdminEntityController::class, 'edit'])->name('entities.edit');

    Route::post('/entities/{entity}/contacts', [AdminEntityContactController::class, 'store'])->name('entities.contacts.store');
    Route::get('/entities/{entity}/contacts/{contact}/edit', [AdminEntityContactController::class, 'edit'])->name('entities.contacts.edit');
    Route::put('/entities/{entity}/contacts/{contact}', [AdminEntityContactController::class, 'update'])->name('entities.contacts.update');

    Route::post('/entities/{entity}/details', [AdminEntityDetailController::class, 'upsert'])->name('entities.details.upsert');

    Route::post('/entities/{entity}/translations', [AdminEntityTranslationController::class, 'store'])->name('entities.translations.store');
    Route::get('/entities/{entity}/translations/{translation}/edit', [AdminEntityTranslationController::class, 'edit'])->name('entities.translations.edit');
    Route::put('/entities/{entity}/translations/{translation}', [AdminEntityTranslationController::class, 'update'])->name('entities.translations.update');
});

require __DIR__.'/auth.php';
