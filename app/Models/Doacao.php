<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doacao extends Model
{
    use HasFactory;

    protected $table = 'doacoes';
    
    protected $fillable = [
        'nome_doador',
        'valor',
        'data_doacao',
        'meio',
        'user_id',
    ];

    protected $casts = [
        'data_doacao' => 'date',
        'valor' => 'float',
    ];
}
