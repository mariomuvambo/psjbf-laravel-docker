<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Comentario;
use App\Models\Gostos;
use Carbon\Carbon;

class AniversarianteController extends Controller
{
    //
    public function data_aniversarianteMes()
    {
        $mesAtual = Carbon::now()->month;

        $aniversariantes = User::whereMonth('data_nascimento', $mesAtual)
            ->select('id', 'nome', 'data_nascimento')
            ->orderByRaw('DAY(data_nascimento) ASC')
            ->get()
            ->map(function ($pessoa) {
                return [
                    'id' => $pessoa->id,
                    'nome' => $pessoa->nome,
                    'data_nascimento' => Carbon::parse($pessoa->data_nascimento)->format('d/m'),
                ];
            });

        return response()->json($aniversariantes);
    }

       // Lista os aniversariantes do mês com curtidas e comentários
    public function aniversariantesDoMes()
    {
        $mesAtual = Carbon::now()->month;

        $aniversariantes = User::whereMonth('data_nascimento', $mesAtual)
            ->select('id', 'nome', 'apelido', 'data_nascimento', 'foto')
            ->withCount([
                'curtidas as total_curtidas',
                'comentariosRecebidos as total_comentariosRecebidos',
                'curtidasRecebidas as total_curtidaRecebidas'
            ])
            ->with([
                'comentarios.user:id,nome'
            ])
            ->get();

        return response()->json($aniversariantes);
    }

     // Curtir um aniversariante (apenas uma vez por usuário)
    public function curtir($id)
    {
        $userId = auth()->id();

        $jaCurtiu = Gostos::where('user_id', $userId)
            ->where('aniversariante_id', $id)
            ->exists();

        if ($jaCurtiu) {
            return response()->json(['message' => 'Você já curtiu esse aniversariante.'], 409);
        }

        Gostos::create([
            'user_id' => $userId,
            'aniversariante_id' => $id,
        ]);

        return response()->json(['message' => 'Curtida registrada com sucesso.']);
    }


    // Adicionar comentário
    public function comentar(Request $request, $id)
    {
        $request->validate(['mensagem' => 'required|string|max:255']);

        $comentario = Comentario::create([
            'user_id' => auth()->id(),
            'aniversariante_id' => $id,
            'mensagem' => $request->mensagem,
        ]);

        return response()->json($comentario->load('user:id,nome'));
    }

    // Atualizar comentário
    public function updateComentario(Request $request, $id)
    {
        $comentario = Comentario::findOrFail($id);

        if ($comentario->user_id !== auth()->id()) {
            return response()->json(['error' => 'Acesso negado.'], 403);
        }

        $request->validate(['mensagem' => 'required|string|max:255']);

        $comentario->update(['mensagem' => $request->mensagem]);

        return response()->json($comentario);
    }

    // Remover comentário
    public function destroyComentario($id)
    {
        $comentario = Comentario::findOrFail($id);

        if ($comentario->user_id !== auth()->id()) {
            return response()->json(['error' => 'Acesso negado.'], 403);
        }

        $comentario->delete();

        return response()->json(['message' => 'Comentário removido com sucesso.']);
    }
    
}
