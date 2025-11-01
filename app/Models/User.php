<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nome',
        'apelido',
        'email',
        'telefone',
        'endereco',
        'genero',
        'data_nascimento',
        'tipo_usuario',
        'foto',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'data_nascimento' => 'date',
        'data_batismo' => 'date',
        'data_crisma' => 'date',
        'data_ordem' => 'date',
    ];

    protected $appends = ['foto_url'];

    // 🔹 Retorna a URL da foto (Cloudflare R2 ou pública)
    public function getFotoUrlAttribute()
    {
        if (!$this->foto) return null;

        // Se já for uma URL completa
        if (str_starts_with($this->foto, 'http')) {
            return $this->foto;
        }

        try {
            // Gera URL temporária válida por 10 minutos
            return Storage::disk('s3')->temporaryUrl($this->foto, now()->addMinutes(10));
        } catch (\Exception $e) {
            Log::error('Erro ao gerar URL temporário da foto: ' . $e->getMessage());
            return null;
        }
    }

    // 🔸 Relacionamentos

    public function avisosLidos()
    {
        return $this->belongsToMany(Aviso::class, 'aviso_user')->withTimestamps();
    }

    public function curtidas()
    {
        return $this->hasMany(Gostos::class, 'user_id');
    }

    public function curtidasRecebidas()
    {
        return $this->hasMany(Gostos::class, 'aniversariante_id');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'user_id');
    }

    public function comentariosRecebidos()
    {
        return $this->hasMany(Comentario::class, 'aniversariante_id');
    }

    public function userMinisters()
    {
        return $this->hasMany(UserMinister::class);
    }

    public function processos()
    {
        return $this->hasMany(Batismo::class);
    }
}
