<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarteiraController;
use App\Http\Controllers\Api\ExtratoController;
use App\Http\Controllers\Api\TransferenciaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas por autenticação
Route::middleware('auth:sanctum')->group(function () {
    // Autenticação
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/revoke-tokens', [AuthController::class, 'revokeAllTokens']);

    // Carteiras
    Route::apiResource('carteiras', CarteiraController::class);
    Route::post('/carteiras/{id}/restore', [CarteiraController::class, 'restore']);

    // Extratos
    Route::get('/extratos', [ExtratoController::class, 'index']);
    Route::get('/extratos/resumo', [ExtratoController::class, 'resumo']);
    Route::get('/extratos/clear-cache', [ExtratoController::class, 'clearCache']);
    Route::get('/carteiras/{carteira}/extratos', [ExtratoController::class, 'carteira']);
    Route::get('/carteiras/{carteira}/extratos/resumo', [ExtratoController::class, 'resumoCarteira']);

    // Transferências Refatorado
    Route::get('/transferencias', [TransferenciaController::class, 'index']);
    Route::post('/transferencias', [TransferenciaController::class, 'store']);
    Route::get('/transferencias/{transfer}', [TransferenciaController::class, 'show']);
    Route::post('/transferencias/buscar-conta', [TransferenciaController::class, 'buscarConta']);
    Route::post('/transferencias/verificar-status', [TransferenciaController::class, 'verificarStatus']);
    Route::get('/beneficiario/{agencia}/{conta}', [TransferenciaController::class, 'buscarBeneficiario']);
    Route::get('/carteira/{carteiraId}/saldo', [TransferenciaController::class, 'buscarSaldoCarteira']);
});

// Rota pública para buscar destinatário (sem autenticação)
Route::post('/buscar-destinatario', [TransferenciaController::class, 'buscarDestinatario']);