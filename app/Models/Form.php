<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;

class Form extends Model
{
    use HasFactory;

    //? nonactifkan timestamps Laravel
    //  Karena timestamp selalu dimasukan otomatis
    //  Saya akan mematikannya
    public $timestamps = false;

    //? Mengambil data question dari model Question
    //  diambil hanya bila data question memiliki form_id sesuai dengan id form
    public function questions()
    {
        return $this->hasMany(Question::class, 'form_id', 'id');
    }

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
