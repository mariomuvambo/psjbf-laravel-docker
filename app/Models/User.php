<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'data_nascimento' => 'date',
        'data_batismo' => 'date',
        'data_crisma' => 'date',
        'data_ordem' => 'date',
    ];
    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute()
{
    if ($this->foto) {
        return url('storage/' . $this->foto);
    }
    return url('images/default-user.png');
}



    // Relacionamento Many-to-Many com Aviso
    public function avisosLidos()
    {
        return $this->belongsToMany(Aviso::class, 'aviso_user')->withTimestamps();
    }

    // Para saber quem curtiu (autor da curtida)
    public function curtidas()
    {
        return $this->hasMany(Gostos::class, 'user_id');
    }

    // Para saber quem recebeu a curtida (aniversariante)
    public function curtidasRecebidas()
    {
        return $this->hasMany(Gostos::class, 'aniversariante_id');
    }

    // Para saber quem comentou (autor do comentário) 
    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'user_id');
    }

    // Para saber quem recebeu o comentário (aniversariante)
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
        return $this->hasMany(Batismo::class); // ou Processo::class se o nome do modelo for diferente
    }

 
}
