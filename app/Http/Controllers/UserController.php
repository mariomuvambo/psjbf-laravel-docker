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

            if (!$user) {
                return response()->json([
                    'error' => 'Token inválido ou usuário não autenticado'
                ], 401);
            }

            $doacoes = Doacao::where('user_id', $user->id)->latest()->get();
            $ministerios = UserMinister::with('regMinister')->where('user_id', $user->id)->get();

            $processos = collect([
                Batismo::where('user_id', $user->id)->latest()->first(),
                Casamento::where('user_id', $user->id)->latest()->first()
            ])->filter();

            $processo = $processos->sortByDesc(fn($p) => $p->created_at)->first();

            $user->makeHidden(['password', 'remember_token']);
            $user->append('foto_url');

            return response()->json([
                'user' => $user,
                'doacoes' => $doacoes,
                'ministerios' => $ministerios,
                'processo' => $processo,
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro no /api/user: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'error' => 'Erro interno no servidor',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
