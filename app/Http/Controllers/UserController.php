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
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        // Força o accessor foto_url a ser resolvido
        $user->foto_url = $user->foto_url;

        // Carrega os relacionamentos, mas com fallback seguro
        $user->loadMissing(['doacoes', 'ministerios', 'processo']);

        return response()->json([
            'user' => $user,
            'doacoes' => $user->doacoes ?? [],
            'ministerios' => $user->ministerios ?? [],
            'processo' => $user->processo ?? null,
        ]);
    } catch (\Throwable $e) {
        \Log::error('Erro em userData: '.$e->getMessage());
        return response()->json([
            'error' => 'Erro interno no servidor',
            'message' => $e->getMessage(),
        ], 500);
    }
}

}
