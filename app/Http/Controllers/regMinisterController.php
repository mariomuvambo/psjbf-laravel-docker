<?php

namespace App\Http\Controllers;

use App\Models\RegMinister;
use Illuminate\Http\Request;

class RegMinisterController extends Controller
{
    // ✅ Listar todos os ministros
    public function index()
    {
        return response()->json(RegMinister::all());
    }

    // ✅ Criar novo ministro
    public function store(Request $request)
    { 
        $validated = $request->validate([
            'new_minister' => 'required|string|max:255',
            'description' => 'nullable|string',
            'response_minister' => 'required|string|max:255',
            'response_adjunto' => 'required|string|max:255',
            'sector_geral' => 'required|string|max:255',
            'sector_minister' => 'required|string|max:255',
        ]);

        $minister = RegMinister::create($validated);

        return response()->json([
            'message' => 'Ministério registrado com sucesso.',
            'data' => $minister
        ], 201);
    }

    // ✅ Exibir ministro específico
    public function show($id)
    {
        $minister = RegMinister::findOrFail($id);
        return response()->json($minister);
    }

    // ✅ Atualizar ministro existente
    public function update(Request $request, $id)
    {
        $minister = RegMinister::findOrFail($id);

        $validated = $request->validate([
            'new_minister' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|nullable',
            'response_minister' => 'sometimes|string|max:255',
            'response_adjunto' => 'sometimes|string|max:255',
            'sector_geral' => 'sometimes|string|max:255',
            'sector_minister' => 'sometimes|string|max:255',
        ]);

        $minister->update($validated);

        return response()->json([
            'message' => 'Ministério atualizado com sucesso.',
            'data' => $minister
        ]);
    }

    // ✅ Remover ministro
    public function destroy($id)
    {
        $minister = RegMinister::findOrFail($id);
        $minister->delete();

        return response()->json(['message' => 'Ministério excluído com sucesso.']);
    }
}
