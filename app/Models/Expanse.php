<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expanse extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function expanse_category()
    {
        return $this->belongsTo(Expanse_category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expanse_operasional()
    {
        return $this->hasOne(Expanse_Operasional::class);
    }
    
    public function expanse_mendadak()
    {
        return $this->hasOne(Expanse_Mendadak::class);
    }

    public function kategori()
    {
        return $this->belongsTo(\App\Models\Expanse_category::class, 'expanse_category_id');
    }

}
