<?php

namespace App\Filament\Pages;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Form;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                // $this->getRememberFormComponent(), // hapus remember
            ]);
    }
}
