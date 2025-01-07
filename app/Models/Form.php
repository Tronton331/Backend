<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    //? nonactifkan timestamps Laravel
    //  Karena timestamp selalu dimasukan otomatis
    //  Saya akan mematikannya
    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'limit_one_response',
        'creator_id',
    ];

    protected $attributes = [
        'limit_one_response'=>'0'
    ];
}
