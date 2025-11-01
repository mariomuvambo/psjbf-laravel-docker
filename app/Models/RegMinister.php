<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegMinister extends Model
{
    use HasFactory;
    protected $fillable = [
        'newMinister',
        'finally',
        'responseMinister',
        'responseAdjunto',
        'SectorGeral',
        'SectorMinister',
    ];

    // Relacionamento com UserMinister
    public function userMinisters()
    {
        return $this->hasMany(UserMinister::class, 'reg_minister_id');
    }

}
