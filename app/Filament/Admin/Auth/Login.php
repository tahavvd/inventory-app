<?php

namespace App\Filament\Admin\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();

        $email = request()->query('email');
        $password = request()->query('password');

        if (is_string($email) || is_string($password)) {
            $this->form->fill([
                'email' => is_string($email) ? $email : null,
                'password' => is_string($password) ? $password : null,
                'remember' => false,
            ]);
        }
    }
}
