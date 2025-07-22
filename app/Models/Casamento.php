<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Casamento extends Model
{
    use HasFactory;
     protected $fillable = [
        'user_id',
        'nome_noivo',
        'nome_noiva',
        'data_casamento',
        'local_casamento',
        'estado',
        'observacoes',
    ];
    public function documentos()
    {
        return $this->hasMany(DocumentoCasamento::class);
    }
     public function user()
    {
        return $this->belongsTo(User::class);
    }
}
