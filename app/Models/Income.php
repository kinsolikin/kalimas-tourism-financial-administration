<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $guarded =['id'];

    public function category()
    {
        return $this->belongsTo(Income_categori::class, 'income_category_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ticketDetail()
    {
        return $this->hasOne(Ticket_income_details::class);
    }

    public function parkingDetail()
    {
        return $this->hasOne(Parking_income_details::class);
    }

    public function toiletDetail()
    {
        return $this->hasOne(Toilet_income_details::class);
    }

    public function restoDetail()
    {
        return $this->hasOne(Resto_income_details::class);
    }

    public function wahanaDetail()
    {
        return $this->hasOne(Wahana_income_details::class);
    }

    public function donationDetail()
    {
        return $this->hasOne(Bantuan_income_details::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

  
}
