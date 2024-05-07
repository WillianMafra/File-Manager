<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function() {
    Route::get('my-files/{folder?}', [FileController::class, 'myFiles'])->where('folder', '(.*)')->name('MyFiles');
    Route::post('folder/create', [FileController::class, 'createFolder'])->name('folder.create');
    Route::post('/file', [FileController::class, 'store'])->name('file.store');
    Route::delete('/file', [FileController::class, 'destroy'])->name('file.delete');
    Route::get('/file/download', [FileController::class, 'download'])->name('file.download');
});

Route::get('/dashboard', function () {
    return redirect()->route('MyFiles');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
