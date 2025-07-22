<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profiluser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'surname',
        'date_birth',
        'nucleo',
        'e_membro',
        'minister',
        'image',
    ];
    protected $casts = [
        'e_membro' => 'boolean',
    ];
    
}
