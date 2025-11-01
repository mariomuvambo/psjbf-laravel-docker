<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMinister extends Model
{
    use HasFactory;

    protected $fillable = [
        'reg_minister_id',
        'user_id',
        'name',
        'surname',
        'contacto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function regMinister()
    {
        return $this->belongsTo(RegMinister::class, 'reg_minister_id');
    }
}
