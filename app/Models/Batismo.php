<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batismo extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome_batizando',
        'data_nascimento',
        'local_nascimento',
        'nome_pai',
        'nome_mae',
        'nome_padrinho',
        'nome_madrinha',
        'documento_identificacao',
        'data_batismo',
        'livro_registo',
        'pagina_registo',
        'codigo_certidao',
        'estado',
        'confirmado',
        'user_id',
        'sacerdote_id',
    ];

    public function sacerdote()
    {
        return $this->belongsTo(User::class, 'sacerdote_id');
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
