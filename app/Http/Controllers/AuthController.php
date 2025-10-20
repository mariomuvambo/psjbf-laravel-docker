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

        // Enviar e-mail de boas-vindas
        // Mail::to($user->email)->queue(new WelcomeMail($user));
        // Mail::to($user->email)->send(new WelcomeMail($user));


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
                // 'user' => $user,
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'email' => $user->email,
                    'tipo_usuario' => $user->tipo_usuario,
                    'role' => $user->role,
                    'foto' => $user->foto ? asset('storage/' . $user->foto) : 'https://via.placeholder.com/40',
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
        // Apenas Admin pode acessar
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $usuarios = User::select('id', 'nome', 'email', 'tipo_usuario', 'foto')->get();

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

    // Só admin pode alterar
    if (Auth::user()->role !== 'admin') {
        return response()->json(['message' => 'Acesso não autorizado.'], 403);
    }

    $validated = $request->validate([
        'nome' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        'tipo_usuario' => 'required|in:fiel,voluntario,sacerdote,admin',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $user->nome = $validated['nome'];
    $user->email = $validated['email'];
    $user->tipo_usuario = $validated['tipo_usuario'];

    if ($request->hasFile('foto')) {
        $user->foto = $request->file('foto')->store('fotos', 'public');
    }

    $user->save();

    return response()->json(['message' => 'Usuário atualizado com sucesso']);
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
