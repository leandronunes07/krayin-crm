<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckUrlInMonitor
{
    public function handle(Request $request, Closure $next)
    {
        // Captura a URL atual
        $currentUrl = $request->url(); // Ou use $request->fullUrl() se precisar da URL com query params

        // Realiza a consulta no banco mysql_monitor
        $result = DB::connection('mysql_monitor')->table('url_list')
            ->where('url', $currentUrl)
            ->first();

        if ($result) {
            // URL válida, podemos continuar a requisição
            Session::put('url_validated', true);
        } else {
            // URL não válida
            Session::put('url_validated', false);
        }

        return $next($request);
    }
}
