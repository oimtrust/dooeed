<?php

namespace App\Domains\Auth\Actions;

use App\Models\User;

class RegisterUser
{
    public function execute(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
    }
}
