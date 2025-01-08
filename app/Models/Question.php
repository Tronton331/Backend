<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'choice_type',
        'choices',
        'is_required',
        'form_id',
    ];

    protected $attributes = [
        'is_required'=>0,
    ];

    use HasFactory;
}
