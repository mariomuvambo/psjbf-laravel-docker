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

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nome' => 'nullable|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB
        ]);

        // 🔹 Atualiza os campos normais
        $user->fill($request->only(['nome', 'apelido', 'telefone', 'endereco']));

        // 🔹 Upload da foto (se enviada)
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = 'fotos/' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Envia para Cloudflare R2
            Storage::disk('s3')->put($path, file_get_contents($file), 'public');

            // Remove a foto antiga, se existir
            if ($user->foto && Storage::disk('s3')->exists($user->foto)) {
                Storage::disk('s3')->delete($user->foto);
            }

            // Atualiza o caminho no banco
            $user->foto = $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'user' => $user->fresh(), // Retorna o user atualizado com foto_url
        ]);
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
