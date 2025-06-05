<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotalExpanse extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function expanse_mendadak()
    {
        return $this->hasMany(Expanse_Mendadak::class);
    }


    public function expanse_operasional()
    {
        return $this->hasMany(Expanse_Operasional::class);
    }

    public function total_income()
    {
        return $this->belongsTo(TotalIncome::class);
    }



}
