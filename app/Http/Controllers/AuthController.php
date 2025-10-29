<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'genero' => 'nullable|in:Masculino,Feminino',
            'data_nascimento' => 'nullable|date',
            'tipo_usuario' => 'required|in:fiel,voluntario,sacerdote',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', 
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Upload da foto (se enviada)
        $fotoPath = null;
        if ($request->hasFile('foto')) { 
            $fotoPath = $request->file('foto')->store('fotos', 'public');
        }
 
        $user = User::create([
            'nome' => $validated['nome'],
            'apelido' => $validated['apelido'] ?? null,
            'email' => $validated['email'],
            'telefone' => $validated['telefone'] ?? null,
            'endereco' => $validated['endereco'] ?? null,
            'genero' => $validated['genero'] ?? null,
            'data_nascimento' => $validated['data_nascimento'] ?? null,
            'tipo_usuario' => $validated['tipo_usuario'],
            'foto' => $fotoPath,
            'password' => Hash::make($validated['password']),
        ]);

        // Mail opcional
        // Mail::to($user->email)->queue(new WelcomeMail($user));

        return response()->json(['message' => 'Usuário registrado com sucesso'], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'email' => $user->email,
                    'tipo_usuario' => $user->tipo_usuario,
                    'role' => $user->role,
                    // ✅ Ajuste 1: usa accessor para URL real da imagem
                    'foto' => $user->foto_url,
                ],
            ], 200);
        }

        return response()->json(['message' => 'Credenciais inválidas'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout efetuado com sucesso'], 200);
    }

    public function listarUsuarios()
    {
        // ✅ Ajuste 2: protege acesso e garante foto_url em cada item
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $usuarios = User::all()->map(function ($u) {
            return [
                'id' => $u->id,
                'nome' => $u->nome,
                'email' => $u->email,
                'tipo_usuario' => $u->tipo_usuario,
                'foto' => $u->foto_url, // acessor
            ];
        });

        $estatisticas = [
            'total' => User::count(),
            'fieis' => User::where('tipo_usuario', 'fiel')->count(),
            'voluntarios' => User::where('tipo_usuario', 'voluntario')->count(),
            'sacerdotes' => User::where('tipo_usuario', 'sacerdote')->count(),
        ];

        return response()->json([
            'usuarios' => $usuarios,
            'estatisticas' => $estatisticas,
        ]);
    }

    public function atualizarUsuario(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'tipo_usuario' => 'required|in:fiel,voluntario,sacerdote,admin',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',

        ]);

        $user->nome = $validated['nome'];
        $user->email = $validated['email'];
        $user->tipo_usuario = $validated['tipo_usuario'];

        if ($request->hasFile('foto')) {
            // ✅ Ajuste 3: substitui a foto antiga e mantém consistência
            if ($user->foto && \Storage::disk('public')->exists($user->foto)) {
                \Storage::disk('public')->delete($user->foto);
            }

            $user->foto = $request->file('foto')->store('fotos', 'public');
        }

        $user->save();

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'user' => $user->fresh(),
        ]);
    }

    public function deletarUsuario($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $user = User::findOrFail($id);

        // (Opcional) apagar foto do storage
        if ($user->foto && \Storage::disk('public')->exists($user->foto)) {
            \Storage::disk('public')->delete($user->foto);
        }

        $user->delete();

        return response()->json(['message' => 'Usuário deletado com sucesso']);
    }
}
