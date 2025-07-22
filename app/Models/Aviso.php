<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aviso extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'date_notify', 'date_realize', 'hora', 'address', 'description',
    ];
    
    protected $casts = [
        'date_notify' => 'date',
        'date_realize' => 'date',
        'hora' => 'datetime:H:i',
    ];

     // Relacionamento Many-to-Many com User
     public function usersLidos()
     {
         return $this->belongsToMany(User::class, 'aviso_user')->withTimestamps();
     }
   
}
