<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'nome' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                    'tipo_usuario' => 'fiel',
                    'foto' => $googleUser->getAvatar(),
                ]
            );

            $token = $user->createToken('google_token')->plainTextToken;

            // Redireciona de volta ao frontend com token + dados
            $redirectUrl = "https://psjbf.onrender.com/login-success?token={$token}&user=" . urlencode(json_encode($user));

            return redirect($redirectUrl);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro no login com Google: ' . $e->getMessage()], 500);
        }
    }
}
