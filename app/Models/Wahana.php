<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Wahana_income_details;

class Wahana extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function wahanaIncome()
    {
        return $this->hasMany(Wahana_income_details::class);
    }
}
