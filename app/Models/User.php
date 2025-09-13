<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];




    public function listShift()
    {
        return $this->hasMany(ListShift::class);
    }
    
    public function employe()
    {
        return $this->hasMany(Employe::class);
    }

    public function expanse_mendadak()
    {
        return $this->hasMany(Expanse_Mendadak::class);
    }

    public function expanse_operasional()
    {
        return $this->hasMany(Expanse_Operasional::class);
    }

    public function total_expanse()
    {
        return $this->hasMany(TotalExpanse::class);
    }

    public function expanse_category()
    {
        return $this->hasMany(Expanse_Category::class);
    }

    public function expanse()
    {
        return $this->hasMany(Expanse::class);
    }

    public function bantuan()
    {
        return $this->hasMany(Bantuan_income_details::class);
    }

    public function income_category()
    {
        return $this->hasMany(Income_categori::class);
    }

    public function income()
    {
        return $this->hasMany(Income::class);
    }

    public function Parking()
    {
        return $this->hasMany(Parking_income_details::class);
    }

    public function Resto()
    {
        return $this->hasMany(Resto_income_details::class);
    }

    public function Ticket()
    {
        return $this->hasMany(Ticket_income_details::class);
    }

    public function Toilet()
    {
        return $this->hasMany(Toilet_income_details::class);
    }

    public function totalexpanse()
    {
        return $this->hasMany(TotalExpanse::class);
    }

    public function Wahana()
    {
        return $this->hasMany(Wahana_income_details::class);
    }

    public function totalIncomes()
    {
        return $this->hasMany(TotalIncome::class);
    }

    public function netIncome()
    {
        return $this->hasOne(NetIncome::class);
    }

    public function shift()
    {
        return $this->hasOne(Shift::class);
    }
}
