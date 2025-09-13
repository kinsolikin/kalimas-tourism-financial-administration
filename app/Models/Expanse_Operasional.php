<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expanse_Operasional extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // biar Laravel tahu nama tabelnya

  

    public function expanse_category()
    {
        return $this->belongsTo(Expanse_Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expanse()
    {
        return $this->belongsTo(Expanse::class);
    }

  
}
