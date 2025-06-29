<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKendaraan extends Model
{
    use HasFactory;

    protected $guarded=['id'];


    public function parkingIncome()
    {
        return $this->hasMany(Parking_income_details::class);
    }
    
}
