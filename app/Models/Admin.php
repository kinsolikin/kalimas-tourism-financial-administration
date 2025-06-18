<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable implements FilamentUser
{
    use Notifiable;


    protected $guarded = ['id'];

    protected $table = 'admins';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        // Atur sesuai kebutuhan. Ini izinkan semua admin.
        return true;

        // Atau jika kamu ingin membatasi misalnya berdasarkan email:
        // return $this->email === 'admin@example.com';
    }
}
