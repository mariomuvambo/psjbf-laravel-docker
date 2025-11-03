<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ProfiluserController;
use App\Http\Controllers\AvisoController;
use App\Http\Controllers\RegMinisterController;
use App\Http\Controllers\UserMinisterController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DoacaoController;
use App\Http\Controllers\AniversarianteController;
use App\Http\Controllers\FinancialHistoryController;
use App\Http\Controllers\CasamentoController;
use App\Http\Controllers\BatismoController;
use App\Http\Controllers\OracaoController;
use App\Http\Controllers\CalendarioLiturgicoController;
use App\Http\Controllers\MassController;
use App\Http\Controllers\UserController;





/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Aqui você registra as rotas da sua API, usando middleware, agrupamentos
| e prefixos conforme necessário.
*/

// Autenticação
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);

// Login com Google
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// oracoes 
    Route::get('/oracoes/ultimas', [OracaoController::class, 'ultimasOracoes']);
    Route::get('/missa/hoje', [MassController::class, 'todayReadings']);

      Route::get('/ping-db', function () {
    try {
        \DB::connection()->getPdo();
        return response()->json(['status' => '✅ Database connected successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});


Route::get('/test-r2', function () {
    try {
        Storage::disk('s3')->put('teste.txt', 'Arquivo de teste Cloudflare R2');
        return response()->json(['success' => true, 'files' => Storage::disk('s3')->allFiles()]);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});


Route::get('/check-pdo', function () {
    return response()->json([
        'pdo_pgsql_loaded' => extension_loaded('pdo_pgsql'),
        'pdo_loaded' => extension_loaded('pdo'),
        'drivers' => \PDO::getAvailableDrivers(),
    ]);
});
Route::get('/check-db-config', function () {
    return response()->json([
        'DB_CONNECTION' => env('DB_CONNECTION'),
        'DB_HOST' => env('DB_HOST'),
        'DB_PORT' => env('DB_PORT'),
        'DB_DATABASE' => env('DB_DATABASE'),
        'DB_USERNAME' => env('DB_USERNAME'),
        'DB_PASSWORD' => env('DB_PASSWORD') ? '********' : null,
    ]);
});

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

Route::get('/db-test', function () {
    try {
        // 1️⃣ Verifica se o .env foi carregado
        $envInfo = [
            'APP_ENV' => env('APP_ENV'),
            'APP_DEBUG' => env('APP_DEBUG'),
            'DB_CONNECTION' => env('DB_CONNECTION'),
            'DB_HOST' => env('DB_HOST'),
            'DB_PORT' => env('DB_PORT'),
            'DB_DATABASE' => env('DB_DATABASE'),
            'DB_USERNAME' => env('DB_USERNAME'),
            'DB_PASSWORD_PRESENT' => env('DB_PASSWORD') ? true : false,
        ];

        // 2️⃣ Testa se PDO e drivers estão carregados
        $pdoInfo = [
            'pdo_loaded' => extension_loaded('pdo'),
            'pdo_pgsql_loaded' => extension_loaded('pdo_pgsql'),
            'drivers' => \PDO::getAvailableDrivers(),
        ];

        // 3️⃣ Testa conexão com o banco
        $dbConnection = DB::connection();
        $dbConnection->getPdo();
        $dbName = $dbConnection->getDatabaseName();
        $dbTest = $dbConnection->select("SELECT NOW() as current_time");

        return response()->json([
            'status' => '✅ Database connected successfully!',
            'env' => $envInfo,
            'pdo' => $pdoInfo,
            'database' => [
                'name' => $dbName,
                'current_time' => $dbTest[0]->current_time ?? null,
            ],
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => '❌ Database connection failed!',
            'error' => $e->getMessage(),
            'env' => $envInfo ?? [],
            'pdo' => $pdoInfo ?? [],
        ], 500);
    }
});


Route::get('/events', [EventController::class, 'index']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {

    // Logout e dados do usuário
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/usuarios', [AuthController::class, 'listarUsuarios']);
    Route::post('/usuarios/{id}', [AuthController::class, 'atualizarUsuario']);
    Route::delete('/usuarios/{id}', [AuthController::class, 'deletarUsuario']);
    Route::get('/user', [UserController::class, 'userData']);


    // calendario Liturgico
    Route::get('/calendario-liturgico', [CalendarioLiturgicoController::class, 'getCalendario']);

    // leituras


    Route::get('/masses', [MassController::class, 'index']);
    Route::post('/masses', [MassController::class, 'store']);
    Route::get('/masses/date/{date}', [MassController::class, 'showByDate']);

    

    // Perfil do usuário
 // Rota personalizada vem primeiro
Route::get('/profilusers/me', [ProfiluserController::class, 'me'])->name('profilusers.me');

// Depois o resource
Route::apiResource('profilusers', ProfiluserController::class);

    
    Route::get('/user/processo', [ProfiluserController::class, 'processo']);


    // Avisos
    Route::apiResource('avisos', AvisoController::class);
    Route::post('avisos/{id}/marcar-como-lido', [AvisoController::class, 'marcarComoLido']);
    Route::get('/estatisticas-avisos', [AvisoController::class, 'estatisticas']);

    // ✝ Ministérios
    Route::apiResource('reg_ministers', RegMinisterController::class);
    Route::apiResource('user_ministers', UserMinisterController::class);
    Route::get('/meus_ministerios', [UserMinisterController::class, 'myMinisters']);


    // Eventos e Doações
    // Route::apiResource('events', EventController::class);
    Route::post('/events', [EventController::class, 'store']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);


    Route::get('events-for-date', [EventController::class, 'eventsForDate']);
    Route::get('/events/monthly-stats', [EventController::class, 'getEventsOfCurrentMonth']);
    Route::apiResource('doacoes', DoacaoController::class);
    Route::get('/doacoes-por-mes', [DoacaoController::class, 'totalDoacoesPorMes']);




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
