<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureConsultaAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get(config('consulta.session_key')) === true) {
            return $next($request);
        }

        return redirect()
            ->route('consulta.login')
            ->with('error', 'Informe a senha para acessar a consulta.');
    }
}
