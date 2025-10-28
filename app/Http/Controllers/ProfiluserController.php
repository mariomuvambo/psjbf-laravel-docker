<?php

namespace App\Http\Controllers;

use App\Models\Profiluser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfiluserController extends Controller
{
    public function index()
    {
        return response()->json(Profiluser::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'surname' => 'required|string',
            'date_birth' => 'required|date',
            'nucleo' => 'required|string',
            'minister' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profilusers', 'public');
        }

        $profiluser = Profiluser::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'date_birth' => $request->date_birth,
            'nucleo' => $request->nucleo,
            'minister' => $request->minister,
            'image' => $imagePath,
        ]);

        return response()->json($profiluser, 201);
    }

    public function show(Profiluser $profiluser)
    {
        $profiluser->image = $profiluser->image
            ? asset('storage/' . $profiluser->image)
            : null;

        return response()->json($profiluser, 200);
    }

    public function update(Request $request, Profiluser $profiluser)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'surname' => 'nullable|string|max:255',
            'date_birth' => 'nullable|date',
            'nucleo' => 'nullable|string|max:255',
            'minister' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // apaga imagem anterior se existir
            if ($profiluser->image && Storage::disk('public')->exists($profiluser->image)) {
                Storage::disk('public')->delete($profiluser->image);
            }

            $imagePath = $request->file('image')->store('profilusers', 'public');
            $validated['image'] = $imagePath;
        }

        $profiluser->update($validated);

        return response()->json($profiluser, 200);
    }

    public function destroy(Profiluser $profiluser)
    {
        if ($profiluser->image && Storage::disk('public')->exists($profiluser->image)) {
            Storage::disk('public')->delete($profiluser->image);
        }

        $profiluser->delete();

        return response()->json(['message' => 'Perfil excluído com sucesso!'], 200);
    }

    public function processo()
    {
        $user = auth()->user();
        $processo = $user->processos()->latest()->first();

        return response()->json($processo);
    }
}
