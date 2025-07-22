<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoCasamento extends Model
{
    use HasFactory;
     protected $fillable = [
        'casamento_id',
        'tipo_documento',
        'arquivo',
    ];

    public function casamento()
    {
        return $this->belongsTo(Casamento::class);
    }
    
}
