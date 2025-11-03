<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'date',
        'time',
        'location',
        'description',
        'image',
    ];

    protected $appends = ['image_url'];
    
   public function getImageUrlAttribute()
{
    if (!$this->image) {
        return null;
    }

    // Se já vier URL completa, retorna como está
    if (str_starts_with($this->image, 'http')) {
        return $this->image;
    }

    // Cria URL temporária (válida por 10 minutos)
    return Storage::disk('s3')->temporaryUrl($this->image, now()->addMinutes(10));
}


}
