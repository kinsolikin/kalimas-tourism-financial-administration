<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function list_shift()
    {
        return $this->hasMany(ListShift::class);
    }

    public function employe()
    {
        return $this->hasMany(Employe::class);
    }

    public function income_categori()
    {
        return $this->hasOne(Income_categori::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}