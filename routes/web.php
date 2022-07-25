<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
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

Route::middleware(['guest'])->group(function () {
    Route::controller(AuthenticationController::class)->group(function(){
        Route::get('/login','index')->name('login');
        Route::post('/login', 'authenticate')->name('authenticate');
    });
});

Route::middleware('auth')->group(function(){
    Route::get('/home', function(){
        return redirect(route('dashboard'));
    });

    Route::get('/', [AuthenticationController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');

    Route::get('/authors', [AuthorController::class, 'index'])->name('author.index');
    Route::get('/authors/import', [AuthorController::class, 'importPage'])->name('author.import-page');
    Route::post('/authors/import', [AuthorController::class, 'import'])->name('author.import-bulk');
    Route::get('/authors/create', [AuthorController::class, 'create'])->name('author.create');
    Route::get('/authors/{author}', [AuthorController::class, 'edit'])->name('author.edit');
    Route::post('/authors/create', [AuthorController::class, 'store'])->name('author.store');
    Route::put('/authors/{author}', [AuthorController::class, 'update'])->name('author.update');
    Route::delete('/authors/{author}', [AuthorController::class, 'delete'])->name('author.delete');

    Route::get('/books', [BookController::class, 'index'])->name('book.index');
    Route::get('/books/import', [BookController::class, 'importPage'])->name('book.import-page');
    Route::post('/books/import', [BookController::class, 'import'])->name('book.import-bulk');
    Route::get('/books/create', [BookController::class, 'create'])->name('book.create');
    Route::get('/books/{book}', [BookController::class, 'edit'])->name('book.edit');
    Route::post('/books/create', [BookController::class, 'store'])->name('book.store');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('book.update');
    Route::delete('/books/{book}', [BookController::class, 'delete'])->name('book.delete');
});
