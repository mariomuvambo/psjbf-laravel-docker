<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userMinister extends Model
{
    use HasFactory;

    protected $fillable = [
        'reg_minister_id', 
        'user_id',
        'name',
        'surname',
        'contacto',
    ];

      // UserMinister.php
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Relacionamento com RegMinister
    public function regMinister()
    { 
        return $this->belongsTo(RegMinister::class, 'reg_minister_id');
    }

    

  



    
    
}
