<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gostos extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'aniversariante_id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aniversariante()
    {
        return $this->belongsTo(User::class, 'aniversariante_id');
    }

}
