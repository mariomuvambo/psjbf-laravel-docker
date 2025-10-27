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
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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

        // Mail::to($user->email)->queue(new WelcomeMail($user));

        return response()->json([
            'message' => 'Usuário registrado com sucesso',
            'user' => [
                'id' => $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'tipo_usuario' => $user->tipo_usuario,
                'foto_url' => $user->foto_url,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

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
                'foto_url' => $user->foto_url,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout efetuado com sucesso']);
    }

    public function listarUsuarios()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $usuarios = User::select('id', 'nome', 'email', 'tipo_usuario', 'foto')->get()
            ->map(function ($user) {
                $user->foto_url = $user->foto_url;
                return $user;
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
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user->update($validated);

        if ($request->hasFile('foto')) {
            $user->foto = $request->file('foto')->store('fotos', 'public');
            $user->save();
        }

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'foto_url' => $user->foto_url,
        ]);
    }

    public function deletarUsuario($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Usuário deletado com sucesso']);
    }
}
