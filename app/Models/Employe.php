<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employe extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function list_shift()
    {
        return $this->belongsToMany(ListShift::class,'shift_employe','employe_id','list_shift_id')->withTimestamps();
    }
}
