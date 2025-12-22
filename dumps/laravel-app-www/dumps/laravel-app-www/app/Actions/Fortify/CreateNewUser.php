<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Validation\Rules;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     */
    public function create(array $input): User
    {
        //dd($input);

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'area' => ['required', 'in:analista,operador'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'area' => strtolower($input['area']), // Por si acaso llegara en mayúscula
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
