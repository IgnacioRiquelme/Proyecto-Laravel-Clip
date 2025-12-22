<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EscaladoIncidente;

class EscaladoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:escalados_incidentes,nombre',
        ]);

        $escalado = EscaladoIncidente::create([
            'nombre' => $request->nombre,
        ]);

        return response()->json([
            'success' => true,
            'data' => $escalado,
            'message' => 'Escalado creado exitosamente'
        ]);
    }
}
