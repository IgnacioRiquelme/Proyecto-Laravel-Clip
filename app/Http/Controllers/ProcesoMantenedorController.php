<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proceso;

class ProcesoMantenedorController extends Controller
{
    public function index(Request $request)
{
    $proceso = null;
    $procesos = null;

    if ($request->filled('buscar')) {
        $buscar = $request->input('buscar');
        $proceso = Proceso::where('trabajo', 'like', "%$buscar%")
            ->orWhere('schedulix_id', 'like', "%$buscar%")
            ->first();
    } else {
        $procesos = Proceso::orderBy('id', 'desc')->get();
    }

    return view('procesos.mantenedor.index', compact('proceso', 'procesos'));
}

    public function create()
    {
        return view('procesos.mantenedor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'trabajo' => 'required|string|max:255',
            'schedulix_id' => 'nullable|string|max:255',
        ]);

        Proceso::create($request->all());

        return redirect()->route('procesos.mantenedor.index')->with('success', 'Proceso creado correctamente');
    }

    public function edit($id)
    {
        $proceso = Proceso::findOrFail($id);
        return view('procesos.mantenedor.edit', compact('proceso'));
    }

    public function update(Request $request, $id)
{
    $proceso = Proceso::findOrFail($id);

    $request->validate([
        'trabajo' => 'required|string|max:255',
        'schedulix_id' => 'nullable|string|max:255',
        'categoria_1' => 'nullable|string|max:100',
        'categoria_2' => 'nullable|string|max:100',
        'ruta_completa' => 'nullable|string',
        'responsable_escalamiento' => 'nullable|string|max:255',
        'observacion' => 'nullable|string|max:500',
        'requiere_solucion_inmediata' => 'nullable|in:1,0',
    ]);

    $proceso->update($request->all());

    return redirect()->route('procesos.mantenedor.index')->with('success', 'Proceso actualizado correctamente');
}
}