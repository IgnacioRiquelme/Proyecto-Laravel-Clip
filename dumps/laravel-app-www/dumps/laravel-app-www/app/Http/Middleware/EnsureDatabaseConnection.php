<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class EnsureDatabaseConnection
{
    public function handle(Request $request, Closure $next)
    {
        try {
            DB::connection()->getPdo();
        } catch (Throwable $e) {
            Log::error("❌ Error de conexión a la base de datos: " . $e->getMessage());

            // Opcional: puedes devolver una vista personalizada o redirigir
            return response()->view('errors.db', [], 500);
        }

        return $next($request);
    }
}
