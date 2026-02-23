<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityController;

Route::prefix('activities')->name('activities.')->group(function () {
  Route::get('data', [ActivityController::class, 'getData'])->name('data');
});

Route::resource('activities', ActivityController::class);
