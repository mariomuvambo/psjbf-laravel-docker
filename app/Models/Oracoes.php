<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oracoes extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'sacerdote_id', 'mensagem'];

    public function remetente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sacerdote()
    {
        return $this->belongsTo(User::class, 'sacerdote_id');
    }
}
