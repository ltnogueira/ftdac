<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultaLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConsultaAuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (session(config('consulta.session_key')) === true) {
            return redirect()->route('cadastros.index');
        }

        return view('consulta.login');
    }

    public function store(ConsultaLoginRequest $request): RedirectResponse
    {
        if (! hash_equals((string) config('consulta.password'), (string) $request->validated('senha'))) {
            return back()
                ->withInput()
                ->withErrors(['senha' => 'Senha invalida.']);
        }

        $request->session()->put(config('consulta.session_key'), true);

        return redirect()
            ->route('cadastros.index')
            ->with('success', 'Acesso liberado com sucesso.');
    }

    public function destroy(): RedirectResponse
    {
        session()->forget(config('consulta.session_key'));

        return redirect()
            ->route('consulta.login')
            ->with('success', 'Sessao encerrada com sucesso.');
    }
}
