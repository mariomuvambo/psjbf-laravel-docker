<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            // gera nome único automaticamente
            $fotoPath = $request->file('foto')->store('fotos', 's3');
            // opcional: tornar arquivo público (R2 via S3 geralmente já usa URL pública)
            // Storage::disk('s3')->setVisibility($fotoPath, 'public');
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

        // fila de email (se quiser)
        Mail::to($user->email)->queue(new WelcomeMail($user));

        // retornar token já logado (útil para SPA)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuário registrado com sucesso',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'tipo_usuario' => $user->tipo_usuario,
                'role' => $user->role ?? null,
                'foto' => $user->foto_url,
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
                'role' => $user->role ?? null,
                'foto' => $user->foto_url,
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout efetuado com sucesso'], 200);
    }

    public function listarUsuarios()
    {
        $auth = Auth::user();
        if (!$auth || $auth->role !== 'admin') {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $usuarios = User::all()->map(function ($u) {
            return [
                'id' => $u->id,
                'nome' => $u->nome,
                'email' => $u->email,
                'tipo_usuario' => $u->tipo_usuario,
                'foto' => $u->foto_url,
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
        $auth = Auth::user();
        if (!$auth || $auth->role !== 'admin') {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => ['required','email','max:255', Rule::unique('users')->ignore($user->id)],
            'tipo_usuario' => 'required|in:fiel,voluntario,sacerdote,admin',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user->nome = $validated['nome'];
        $user->email = $validated['email'];
        $user->tipo_usuario = $validated['tipo_usuario'];

        if ($request->hasFile('foto')) {
            // apagar foto antiga (se existir)
            if ($user->foto && Storage::disk('s3')->exists($user->foto)) {
                Storage::disk('s3')->delete($user->foto);
            }
            $user->foto = $request->file('foto')->store('fotos', 's3');
        }

        $user->save();

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'user' => [
                'id' => $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'tipo_usuario' => $user->tipo_usuario,
                'foto' => $user->foto_url,
            ],
        ]);
    }

    public function deletarUsuario($id)
    {
        $auth = Auth::user();
        if (!$auth || $auth->role !== 'admin') {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $user = User::findOrFail($id);

        if ($user->foto && Storage::disk('s3')->exists($user->foto)) {
            Storage::disk('s3')->delete($user->foto);
        }

        $user->delete();

        return response()->json(['message' => 'Usuário deletado com sucesso']);
    }
}
