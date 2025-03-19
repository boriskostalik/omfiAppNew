<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\AboutController;


Route::get('/', [HomeController::class, 'index'])->name('home');
//Route::get('/year/{year}/issue/{issue}', [HomeController::class, 'showIssue'])->name('issue.show');
//Route::get('/', function () {
    //return Inertia::render('HomePage', [
        //'canLogin' => Route::has('login'),
        //'canRegister' => Route::has('register'),
        //'laravelVersion' => Application::VERSION,
        //'phpVersion' => PHP_VERSION,

    //]);
//});
Route::get('/publications', [PublicationController::class, 'index'])->name('publications.index');
Route::get('/publications/{year}', [HomeController::class, 'showYear']);
Route::get('/publications/{year}/{number}', [HomeController::class, 'showIssue']);

// Stránka autorov
Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
Route::get('/authors/{id}', [AuthorController::class, 'detail'])->name('authors.detail');


// Stránka "O Časopise"
Route::get('/about', [AboutController::class, 'index']);

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/publications', [PublicationController::class, 'indexDashboard'])->name('publications.dashboard');
    Route::post('/dashboard/publications', [PublicationController::class, 'store'])->name('publications.store');
    Route::put('/dashboard/publications/{publication}', [PublicationController::class, 'update'])->name('publications.update');
    Route::delete('/dashboard/publications/{publication}', [PublicationController::class, 'destroy'])->name('publications.destroy');

    Route::get('/dashboard/authors', [PublicationController::class, 'indexDashboard'])->name('authors.dashboard');
    Route::post('/dashboard/authors', [PublicationController::class, 'store'])->name('authors.store');
    Route::put('/dashboard/authors/{author}', [PublicationController::class, 'update'])->name('authors.update');
    Route::delete('/dashboard/authors/{author}', [PublicationController::class, 'destroy'])->name('authors.destroy');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
