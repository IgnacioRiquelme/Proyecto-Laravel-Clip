<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\Request;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        // Redirigir según el área del usuario
        if ($user->area === 'Analista') {
            return redirect()->intended(route('menu.analista'));
        } elseif ($user->area === 'Operador') {
            return redirect()->intended(route('menu.operador'));
        }

        // Redirección por defecto si no coincide con ninguna área
        return redirect()->intended('/');
    }
}

