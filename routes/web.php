<?php

use Illuminate\Support\Facades\Route;
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

/*
|--------------------------------------------------------------------------
| Rotas Públicas (Sem Autenticação)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

// Logout protegido
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Requer Autenticação)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Página inicial / dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Endpoints AJAX e rotas personalizadas
    |--------------------------------------------------------------------------
    */
    // ✅ Endpoint usado pela tela "Nova Conta a Pagar"
    Route::get('/compras/dados/{id}', [CompraController::class, 'dadosAjax'])
        ->name('compras.dados');

    // Compras - ações rápidas
    Route::post('/compras/{id}/finalizar', [CompraController::class, 'finalizar'])
        ->name('compras.finalizar');
    Route::post('/compras/{id}/cancelar', [CompraController::class, 'cancelar'])
        ->name('compras.cancelar');

    // Contas a pagar - alternar status
    Route::patch('/contas-pagar/{id}/toggle-status', [ContasPagarController::class, 'toggleStatus'])
        ->name('contas-pagar.toggle-status');

    // Fluxo de Caixa
    Route::prefix('fluxo-caixa')->name('fluxo-caixa.')->group(function () {
        Route::get('/', [FluxoCaixaController::class, 'index'])->name('index');
        Route::get('/export/pdf', [FluxoCaixaController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [FluxoCaixaController::class, 'exportCsv'])->name('export.excel');
    });

    // Calendário (caso precise endpoints separados para AJAX)
    Route::get('/calendario/eventos', [CalendarioController::class, 'getEventos'])
        ->name('calendario.eventos');

    /*
    |--------------------------------------------------------------------------
    | Módulos principais do sistema
    |--------------------------------------------------------------------------
    */
    Route::resources([
        'clientes'       => ClienteController::class,
        'fornecedores'   => FornecedorController::class,
        'compras'        => CompraController::class,
        'contas-pagar'   => ContasPagarController::class,
        'contas-receber' => ContaReceberController::class,
        'usuarios'       => UsuarioController::class,
        'calendario'     => CalendarioController::class,
    ]);
});

/*
|--------------------------------------------------------------------------
| Rota Fallback (404 personalizada)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
