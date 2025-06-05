<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TotalIncome extends Model
{
    use HasFactory;

    use SoftDeletes;


    protected $guarded = ['id'];

  
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function total_expanse()
    {
        return $this->hasMany(TotalExpanse::class);
    }

    public function net_income()
    {
        return $this->hasOne(NetIncome::class);
    }
}
