<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ProfiluserController;
use App\Http\Controllers\AvisoController;
use App\Http\Controllers\regMinisterController;
use App\Http\Controllers\userMinisterController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DoacaoController;
use App\Http\Controllers\AniversarianteController;
use App\Http\Controllers\FinancialHistoryController;
use App\Http\Controllers\CasamentoController;
use App\Http\Controllers\BatismoController;
use App\Http\Controllers\OracaoController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Aqui você registra as rotas da sua API, usando middleware, agrupamentos
| e prefixos conforme necessário.
*/

Route::get('/example', function () {
    return response()->json(['message' => 'Hello from Laravel!']);
});

// Autenticação
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);

// Login com Google
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {

    // Logout e dados do usuário
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/usuarios', [AuthController::class, 'listarUsuarios']);
    Route::post('/usuarios/{id}', [AuthController::class, 'atualizarUsuario']);
    Route::delete('/usuarios/{id}', [AuthController::class, 'deletarUsuario']);
    Route::get('/user', fn(Request $request) => $request->user());

    // Perfil do usuário
    Route::apiResource('profilusers', ProfiluserController::class);
    Route::get('/user/processo', [ProfiluserController::class, 'processo']);


    // Avisos
    Route::apiResource('avisos', AvisoController::class);
    Route::post('avisos/{id}/marcar-como-lido', [AvisoController::class, 'marcarComoLido']);
    Route::get('/estatisticas-avisos', [AvisoController::class, 'estatisticas']);

    // ✝ Ministérios
    Route::apiResource('reg_ministers', regMinisterController::class);
    Route::apiResource('user_ministers', userMinisterController::class);
    Route::get('/meus_ministerios', [UserMinisterController::class, 'myMinisters']);


    // Eventos e Doações
    Route::apiResource('events', EventController::class);
    Route::apiResource('doacoes', DoacaoController::class);

    // Administração de usuários
    Route::get('/admin/usuarios', [AuthController::class, 'listarUsuarios']);
    Route::put('/admin/usuarios/{id}', [AuthController::class, 'atualizarUsuario']);

    // Aniversariantes
    Route::get('/data_aniversarianteMes', [AniversarianteController::class, 'data_aniversarianteMes']);
    Route::get('/aniversariantes', [AniversarianteController::class, 'aniversariantesDoMes']);
    Route::post('/aniversariantes/{id}/curtir', [AniversarianteController::class, 'curtir']);
    Route::post('/aniversariantes/{id}/comentar', [AniversarianteController::class, 'comentar']);
    Route::put('/comentarios/{id}', [AniversarianteController::class, 'updateComentario']);
    Route::delete('/comentarios/{id}', [AniversarianteController::class, 'destroyComentario']);

    // Histórico Financeiro (Apenas para autorizados)
    Route::middleware('finance.access')->prefix('financeiro')->group(function () {
        Route::get('/', [FinancialHistoryController::class, 'index']);
        Route::get('/pdf', [FinancialHistoryController::class, 'exportPdf']);
    });

    // Casamentos 
     Route::resource('casamentos', CasamentoController::class);

      // Batismo
    Route::put('/batismos/{id}/estado', [BatismoController::class, 'atualizarEstado']);
    Route::get('/batismos/pendentes', [BatismoController::class, 'pendentes']);
    Route::get('/batismos/aprovados', [BatismoController::class, 'aprovados']);
    Route::get('/batismos/rejeitados', [BatismoController::class, 'rejeitados']);
    Route::get('/batismos/em_analise', [BatismoController::class, 'emAnalise']);


    Route::apiResource('batismos', BatismoController::class);
    Route::put('/batismos/{id}/estado', [BatismoController::class, 'atualizarEstado']);

    // ORACOES
    Route::get('/pedir-oracao', [OracaoController::class, 'index'])->name('oracao.index');
    Route::post('/pedir-oracao', [OracaoController::class, 'store'])->name('oracao.store');
    Route::post('/oracoes/{id}/marcar-lida', [OracaoController::class, 'marcarComoLida']);



    // 🛡 Rotas apenas para Administradores
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', fn() => response()->json(['message' => 'Welcome, Admin!']));
    });
});
