<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegMinister extends Model
{
    use HasFactory;
   
        protected $fillable = [
            'new_minister',
            'description',
            'response_minister',
            'response_adjunto',
            'sector_geral',
            'sector_minister',
        ];



    // Relacionamento com UserMinister
    public function userMinisters()
    {
        return $this->hasMany(UserMinister::class, 'reg_minister_id');
    }

}
