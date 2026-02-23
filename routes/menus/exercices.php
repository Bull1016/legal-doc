<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExerciceController;

Route::prefix('mandates')->name('mandates.')->group(function () {
  Route::get('data', [ExerciceController::class, 'getData'])->name('data');

  Route::prefix('{mandate}/team')->name('team.')->group(function () {
    Route::get('', [ExerciceController::class, 'getTeam'])->name('get');
    Route::post('store', [ExerciceController::class, 'storeTeam'])->name('store');
    Route::get('roles', [ExerciceController::class, 'getAvailableRoles'])->name('roles');
    Route::get('members', [ExerciceController::class, 'getAvailableMembers'])->name('members');

    Route::prefix('{team}')->group(function () {
      Route::put('update', [ExerciceController::class, 'updateTeam'])->name('update');
      Route::delete('destroy', [ExerciceController::class, 'destroyTeam'])->name('destroy');
    });
  });
});

Route::resource('mandates', ExerciceController::class);
