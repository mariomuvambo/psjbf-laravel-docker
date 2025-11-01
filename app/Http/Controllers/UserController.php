<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doacao;
use App\Models\UserMinister;
use App\Models\Batismo;
use App\Models\Casamento;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function userData(Request $request)
    {
        try {
            $user = $request->user();

            // 🔒 Verifica autenticação
            if (!$user) {
                return response()->json([
                    'error' => 'Token inválido ou usuário não autenticado'
                ], 401);
            }

            // 🔹 Doações do usuário
            $doacoes = Doacao::where('user_id', $user->id)
                ->latest()
                ->get();

            // 🔹 Ministérios do usuário
            $ministerios = UserMinister::with('regMinister')
                ->where('user_id', $user->id)
                ->get();

            // 🔹 Processo ativo (batismo ou casamento)
            $batismo = Batismo::where('user_id', $user->id)->latest()->first();
            $casamento = Casamento::where('user_id', $user->id)->latest()->first();
            $processo = $batismo ?? $casamento;

            // 🔹 Oculta dados sensíveis e adiciona foto_url
            $user->makeHidden(['password', 'remember_token']);
            $user->append('foto_url');

            return response()->json([
                'user' => $user,
                'doacoes' => $doacoes,
                'ministerios' => $ministerios,
                'processo' => $processo,
            ]);

        } catch (\Throwable $e) {
            // 📜 Loga o erro completo para depuração
            Log::error('Erro no /api/user: '.$e->getMessage().' em '.$e->getFile().':'.$e->getLine());
            
            return response()->json([
                'error' => 'Erro interno no servidor',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
