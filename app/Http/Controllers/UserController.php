<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doacao;
use App\Models\UserMinister;
use App\Models\Batismo;
use App\Models\Casamento;

class UserController extends Controller
{
    public function userData(Request $request)
    {
        $user = $request->user();

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

        // 🔹 Garante que o campo foto_url venha do Accessor do model User
        $user->makeHidden(['password', 'remember_token']);
        $user->append('foto_url');

        return response()->json([
            'user' => $user,
            'doacoes' => $doacoes,
            'ministerios' => $ministerios,
            'processo' => $processo,
        ]);
    }
}
