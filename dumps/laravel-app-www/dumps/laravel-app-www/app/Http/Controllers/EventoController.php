<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;

class EventoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:eventos,nombre',
        ]);

        $evento = Evento::create(['nombre' => $request->nombre]);

        return response()->json(['success' => true, 'id' => $evento->id]);
    }
}
