<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'date_birth' => 'date',
    ];

    protected $appends = ['image_url'];

    // ✅ Gera URL pública temporária da imagem (Cloudflare R2)
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // Se já vier URL completa, retorna direto
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // Gera URL temporária válida por 10 minutos
        return Storage::disk('s3')->temporaryUrl($this->image, now()->addMinutes(10));
    }
}
