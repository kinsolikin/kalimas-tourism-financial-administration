<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListShift extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function employe()
    {
        return $this->belongsToMany(Employe::class,'shift_employe','list_shift_id','employe_id')->withTimestamps();
    }
}
