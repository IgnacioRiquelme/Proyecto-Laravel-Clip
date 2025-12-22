<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toResponse($request)
    {
        $user = $request->user();
        
        // Redirigir a diferentes rutas dependiendo del área del usuario
        if ($user->area === 'Analista') {
            return redirect()->route('menu.analista');
        }

        return redirect()->route('menu.operador');
    }
}
