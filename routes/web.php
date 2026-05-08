<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\InventarioRiscoController;
use App\Http\Controllers\PlanoAcaoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TreinamentoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('empresas', EmpresaController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update'])
        ->middleware('role:admin_empresa');

    Route::resource('inventarios', InventarioRiscoController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::prefix('inventarios/{inventario}/perigos')->name('inventarios.perigos.')->group(function () {
        Route::post('/', [InventarioRiscoController::class, 'storePerigo'])->name('store');
        Route::put('/{perigo}', [InventarioRiscoController::class, 'updatePerigo'])->name('update');
        Route::delete('/{perigo}', [InventarioRiscoController::class, 'destroyPerigo'])->name('destroy');
    });

    Route::get('/planos', [PlanoAcaoController::class, 'index'])->name('planos.index');

    Route::prefix('perigos/{perigo}')->name('perigos.')->group(function () {
        Route::post('/planos', [PlanoAcaoController::class, 'store'])->name('planos.store');
    });

    Route::prefix('planos/{plano}')->name('planos.')->group(function () {
        Route::patch('/status', [PlanoAcaoController::class, 'updateStatus'])->name('status');
        Route::post('/evidencias', [PlanoAcaoController::class, 'uploadEvidencia'])->name('evidencias.store');
        Route::delete('/evidencias/{evidencia}', [PlanoAcaoController::class, 'destroyEvidencia'])->name('evidencias.destroy');
    });

    Route::resource('treinamentos', TreinamentoController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::prefix('treinamentos/{treinamento}')->name('treinamentos.')->group(function () {
        Route::post('/participantes', [TreinamentoController::class, 'storeParticipantes'])->name('participantes.store');
        Route::delete('/participantes/{participante}', [TreinamentoController::class, 'destroyParticipante'])->name('participantes.destroy');
    });

    Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
    Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
    Route::get('/documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
});

require __DIR__.'/auth.php';
