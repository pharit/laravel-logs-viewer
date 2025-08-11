<?php

use Illuminate\Support\Facades\Route;
use Iaa\LaravelLogsViewer\Http\Controllers\LogsViewerController;

Route::get('/', [LogsViewerController::class, 'index'])->name('logs-viewer.index');
Route::get('/download', [LogsViewerController::class, 'download'])->name('logs-viewer.download');
Route::delete('/', [LogsViewerController::class, 'clear'])->name('logs-viewer.clear');


