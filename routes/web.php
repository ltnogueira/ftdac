<?php

use App\Http\Controllers\CadastroController;
use App\Http\Controllers\ConsultaAuthController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/cadastro');

Route::prefix('cadastro')->group(function () {
    Route::get('/', [CadastroController::class, 'create'])->name('cadastros.create');
    Route::post('/', [CadastroController::class, 'store'])->name('cadastros.store');

    Route::get('/consulta/login', [ConsultaAuthController::class, 'create'])->name('consulta.login');
    Route::post('/consulta/login', [ConsultaAuthController::class, 'store'])->name('consulta.authenticate');
    Route::post('/consulta/logout', [ConsultaAuthController::class, 'destroy'])->name('consulta.logout');

    Route::middleware('consulta.access')->group(function () {
        Route::get('/consulta', [CadastroController::class, 'index'])->name('cadastros.index');
        Route::get('/consulta/exportar', [CadastroController::class, 'export'])->name('cadastros.export');
        Route::get('/{cadastro}/editar', [CadastroController::class, 'edit'])->name('cadastros.edit');
        Route::put('/{cadastro}', [CadastroController::class, 'update'])->name('cadastros.update');
        Route::delete('/{cadastro}', [CadastroController::class, 'destroy'])->name('cadastros.destroy');
    });
});
