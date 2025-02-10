<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ProjectServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $dbMonitor = DB::connection('mysql_monitor')->table('projects')
            ->where('url', request()->getHost()) // Pegando o domínio atual
            ->first();

        // Compartilhar a variável `$dbMonitor` com todas as views
        View::share('dbMonitor', $dbMonitor);
    }
}
