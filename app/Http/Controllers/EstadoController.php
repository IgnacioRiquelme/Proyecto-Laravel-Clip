<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estado;

class EstadoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:estados,nombre',
        ]);

        $estado = Estado::create([
            'nombre' => $request->nombre,
        ]);

        return response()->json([
            'success' => true,
            'data' => $estado,
            'message' => 'Estado creado exitosamente'
        ]);
    }
}
