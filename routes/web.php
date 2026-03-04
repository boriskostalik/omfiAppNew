<?php
use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\SearchController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('publications/detail/{id}', [PublicationController::class, 'detail'])->name('publications.detail');
Route::get('/archive', [ArchiveController::class, 'index'])->name('archive.index');
Route::get('/archive/issue/{issue}', [ArchiveController::class, 'showIssue'])->name('archive.issue');
Route::get('/authors/{id}', [AuthorController::class, 'detail'])->name('authors.detail');
Route::get('/about', [AboutController::class, 'index']);
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/issues', [IssueController::class, 'index'])->name('issues.dashboard');
    Route::post('/dashboard/issues', [IssueController::class, 'store'])->name('issues.store');
    Route::put('/dashboard/issues/{issue}', [IssueController::class, 'update'])->name('issues.update');
    Route::delete('/dashboard/issues/{issue}', [IssueController::class, 'destroy'])->name('issues.destroy');
    Route::get('/dashboard/publications', [PublicationController::class, 'indexDashboard'])->name('publications.dashboard');
    
    Route::post('/dashboard/publications', [PublicationController::class, 'store'])->name('publications.store');
    Route::put('/dashboard/publications/{publication}', [PublicationController::class, 'update'])->name('publications.update');
    Route::delete('/dashboard/publications/{publication}', [PublicationController::class, 'destroy'])->name('publications.destroy');

    Route::get('/dashboard/authors', [AuthorController::class, 'indexDashboard'])->name('authors.dashboard');
    Route::post('/dashboard/authors', [AuthorController::class, 'store'])->name('authors.store');
    Route::put('/dashboard/authors/{author}', [AuthorController::class, 'update'])->name('authors.update');
    Route::delete('/dashboard/authors/{author}', [AuthorController::class, 'destroy'])->name('authors.destroy');

});
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard/users', [UserController::class, 'indexDashboard'])->name('users.dashboard');
    Route::post('/dashboard/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/dashboard/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/dashboard/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
