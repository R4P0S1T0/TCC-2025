<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ContasPagarController;
use App\Http\Controllers\ContaReceberController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\FluxoCaixaController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;




Route::middleware(['auth'])->group(function () {
    Route::resource('compras', CompraController::class);

    // Rotas extras para ações rápidas
    Route::post('/compras/{id}/finalizar', [CompraController::class, 'finalizar'])->name('compras.finalizar');
    Route::post('/compras/{id}/cancelar', [CompraController::class, 'cancelar'])->name('compras.cancelar');
});


// Rotas públicas
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/compras/dados/{id}', [CompraController::class, 'dadosAjax'])->name('compras.dados');


// Rotas protegidas
Route::middleware(['auth'])->group(function () {
    // Página principal (dashboard)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('clientes', ClienteController::class);
    Route::resource('clientes', ClienteController::class);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/fluxo-caixa', [FluxoCaixaController::class, 'index'])->name('fluxo-caixa.index');
    Route::get('/fluxo-caixa/export/pdf', [FluxoCaixaController::class, 'exportPdf'])->name('fluxo-caixa.export.pdf');
    Route::get('/fluxo-caixa/export/csv', [FluxoCaixaController::class, 'exportCsv'])->name('fluxo-caixa.export.excel');


    // Endpoint AJAX para puxar dados da compra no Contas a Pagar
    Route::get('/compras/{id}/dados', [CompraController::class, 'dadosAjax'])
        ->name('compras.dados')
        ->middleware('auth');

    // Módulos do sistema
    Route::resource('fornecedores', FornecedorController::class);
    Route::resource('compras', CompraController::class);
    Route::resource('contas-pagar', ContasPagarController::class);
    Route::patch('/contas-pagar/{id}/toggle-status', [ContasPagarController::class, 'toggleStatus'])
        ->name('contas-pagar.toggle-status');
    Route::resource('contas-receber', ContaReceberController::class);
    Route::resource('usuarios', UsuarioController::class);

    // Calendário
    Route::get('calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('calendario/create', [CalendarioController::class, 'create'])->name('calendario.create');
    Route::get('calendario/eventos', [CalendarioController::class, 'getEventos'])->name('calendario.eventos');
    Route::post('calendario', [CalendarioController::class, 'store'])->name('calendario.store');
    Route::put('calendario/{id}', [CalendarioController::class, 'update'])->name('calendario.update');
    Route::delete('calendario/{id}', [CalendarioController::class, 'destroy'])->name('calendario.destroy');
    Route::resource('calendario', CalendarioController::class)->middleware('auth');

    // Fluxo de Caixa
    Route::get('fluxo-caixa', [FluxoCaixaController::class, 'index'])->name('fluxo-caixa.index');
    Route::get('fluxo-caixa/export/pdf', [FluxoCaixaController::class, 'exportPdf'])->name('fluxo-caixa.export.pdf');
    Route::get('fluxo-caixa/export/excel', [FluxoCaixaController::class, 'exportExcel'])->name('fluxo-caixa.export.excel');
});

