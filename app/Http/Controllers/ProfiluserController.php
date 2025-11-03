<?php

namespace App\Http\Controllers;

use App\Models\Profiluser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class ProfiluserController extends Controller
{
    public function index()
    {
        $profiles = Profiluser::all();
        return response()->json($profiles, 200);
    }

     public function me()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado'], 401);
        }

        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'date_birth' => 'required|date',
            'nucleo' => 'required|string|max:255',
            'e_membro' => 'required|boolean',
            'minister' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('profilusers', 's3');
        }

        $profile = Profiluser::create($validated);

        return response()->json([
            'message' => '✅ Perfil criado com sucesso!',
            'data' => $profile
        ], 201);
    }

    public function show($id)
    {
        $profile = Profiluser::findOrFail($id);
        return response()->json($profile, 200);
    }

    public function update(Request $request, $id)
    {
        $profile = Profiluser::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'surname' => 'nullable|string|max:255',
            'date_birth' => 'nullable|date',
            'nucleo' => 'nullable|string|max:255',
            'e_membro' => 'nullable|boolean',
            'minister' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Deleta imagem antiga do R2
            if ($profile->image && Storage::disk('s3')->exists($profile->image)) {
                Storage::disk('s3')->delete($profile->image);
            }

            // Envia nova
            $validated['image'] = $request->file('image')->store('profilusers', 's3');
        }

        $profile->update($validated);

        return response()->json([
            'message' => '✅ Perfil atualizado com sucesso!',
            'data' => $profile
        ], 200);
    }

    public function destroy($id)
    {
        $profile = Profiluser::findOrFail($id);

        if ($profile->image && Storage::disk('s3')->exists($profile->image)) {
            Storage::disk('s3')->delete($profile->image);
        }

        $profile->delete();

        return response()->json(['message' => '🗑️ Perfil removido com sucesso.']);
    }
}
