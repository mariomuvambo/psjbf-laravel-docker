<?php

namespace App\Http\Controllers;

use App\Models\RegMinister;
use App\Models\UserMinister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMinisterController extends Controller
{
    public function index()
    {

        $user = Auth::user();

        $userMinisters = UserMinister::with('regMinister')
        ->where('user_id', $user->id)
        ->get();

    return response()->json($userMinisters);
        
    }

    public function store(Request $request) 
    {
        $request->validate([
            'reg_minister_id' => 'required|exists:reg_ministers,id',
        ]);

        $user = Auth::user();

        // Verificar se já existe vínculo com este ministério
        $exists = UserMinister::where('user_id', $user->id)
            ->where('reg_minister_id', $request->reg_minister_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Este usuário já está registrado neste ministério.',
            ], 409); // 409 Conflict
        }

        // Criar novo vínculo
        $userMinister = UserMinister::create([
            'user_id' => $user->id,
            'reg_minister_id' => $request->reg_minister_id,
            'name' => $user->nome,
            'surname' => $user->apelido,
            'contacto' => $user->telefone,
        ]);

        return response()->json([
            'message' => 'Usuário registrado no ministério com sucesso!',
            'data' => $userMinister
        ], 201);
    }


    public function show($id)
    {
        return response()->json(UserMinister::with(['regMinister', 'user'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'reg_minister_id' => 'required|exists:reg_ministers,id',
        ]);

        $userMinister = UserMinister::findOrFail($id);
        $user = Auth::user();

        $userMinister->update([
            'reg_minister_id' => $request->reg_minister_id,
            'name' => $user->nome,
            'surname' => $user->apelido,
            'contacto' => $user->telefone,
        ]);

        return response()->json([
            'message' => 'Registro de ministério atualizado com sucesso!',
            'data' => $userMinister
        ]);
    }

    public function destroy($id)
    {
        UserMinister::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Registro de ministério removido com sucesso!'
        ]);
    }

   public function myMinisters()
    {
        $userId = Auth::id();

        // Buscar o reg_minister vinculado ao usuário autenticado via user_ministers
        $regMinister = RegMinister::whereHas('user_ministers', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->first(); 
        return response()->json($regMinister);
    }

}
