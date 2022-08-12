<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\EbookController;
use App\Http\Controllers\PodTransactionController;
use App\Http\Controllers\UserController;
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

    Route::controller(UserController::class)->group(function(){
        Route::get('/profile', 'index')->name('user.profile');
        Route::post('/profile', 'updateProfile')->name('user.update-profile');
        Route::get('/profile/change-password', 'editPassword')->name('user.edit-password');
        Route::post('/profile/change-password', 'updatePassword')->name('user.update-password');
    });

    Route::controller(AuthenticationController::class)->group(function(){
        Route::get('/', 'dashboard')->name('dashboard');
        Route::post('/logout', 'logout')->name('logout');
    });

    Route::controller(AuthorController::class)->group(function(){
        Route::get('/authors', 'index')->name('author.index');
        Route::get('/authors/import', 'importPage')->name('author.import-page');
        Route::post('/authors/import', 'import')->name('author.import-bulk');
        Route::get('/authors/create', 'create')->name('author.create');
        Route::get('/authors/{author}', 'edit')->name('author.edit');
        Route::post('/authors/create', 'store')->name('author.store');
        Route::put('/authors/{author}', 'update')->name('author.update');
        Route::delete('/authors/{author}', 'delete')->name('author.delete');
    });

    Route::controller(BookController::class)->group(function(){
        Route::get('/books','index')->name('book.index');
        Route::get('/books/import','importPage')->name('book.import-page');
        Route::post('/books/import', 'import')->name('book.import-bulk');
        Route::get('/books/create', 'create')->name('book.create');
        Route::get('/books/{book}', 'edit')->name('book.edit');
        Route::post('/books/create', 'store')->name('book.store');
        Route::put('/books/{book}', 'update')->name('book.update');
        Route::delete('/books/{book}', 'delete')->name('book.delete');
    });

    Route::controller(PodTransactionController::class)->prefix('pod')->group(function(){
        Route::get('/','index')->name('pod.index');
        Route::get('/search', 'search')->name('pod.search');
        Route::get('/import','importPage')->name('pod.import-page');
        Route::post('/import', 'import')->name('pod.import-bulk');
        Route::get('/create', 'create')->name('pod.create');
        Route::post('/create', 'store')->name('pod.store');
        Route::get('/{pod}/edit', 'edit')->name('pod.edit');
        Route::put('/{pod}', 'update')->name('pod.update');
        Route::get('/{pod}', 'delete')->name('pod.delete');
    });

    Route::controller(EbookController::class)->prefix('ebook')->group(function(){
        Route::get('/','index')->name('ebook.index');
        Route::get('/search', 'search')->name('ebook.search');
        Route::get('/create', 'create')->name('ebook.create');
        Route::get('/import', 'importPage')->name('ebook.import-page');
        Route::post('/import', 'import')->name('ebook.import-bulk');
        Route::post('/create', 'store')->name('ebook.store');
        Route::get('/{ebook}/edit', 'edit')->name('ebook.edit');
        Route::put('/{ebook}', 'update')->name('ebook.update');
        Route::get('/{ebook}', 'delete')->name('ebook.delete');
    });
});


