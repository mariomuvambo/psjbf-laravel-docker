<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mass extends Model
{
    use HasFactory;

     protected $fillable = [
        'date',
        'time',
        'liturgical_day',
        'first_reading',
        'first_reader',
        'psalm',
        'psalm_reader',
        'second_reading',
        'second_reader',
        'gospel',
        'celebrant',
        'notes',
    ];
}
