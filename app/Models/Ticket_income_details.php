<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket_income_details extends Model
{
    use HasFactory;

    protected $guarded =['id'];

    public function income()
    {
        return $this->belongsTo(Income::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
