<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;

Route::prefix('agendas')->name('agendas.')->group(function () {
  Route::get('events', [AgendaController::class, 'events'])->name('events');
});

Route::resource('agendas', AgendaController::class);
