<?php

namespace App\Http\Controllers\Auth;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\Request;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->area === 'Analista') {
            return redirect()->route('menu.analista');
        } elseif ($user->area === 'Operaciones') {
            return redirect()->route('menu.operador');
        }

        return redirect()->intended('/dashboard');
    }
}
