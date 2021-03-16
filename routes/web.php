<?php

use Illuminate\Support\Facades\Route;
use SrcLab\AltLog\Http\Controllers\LogController;

Route::get('/logs', [LogController::class, 'index'])->name('index');

Route::prefix('log')->name('log.')->group(function () {
    Route::post('/list', [LogController::class, 'list'])->name('list');
    Route::post('/get', [LogController::class, 'get'])->name('get');
    Route::post('/delete', [LogController::class, 'delete'])->name('delete');
});
