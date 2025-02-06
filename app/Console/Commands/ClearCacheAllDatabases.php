<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class ClearCacheAllDatabases extends Command
{
    protected $signature = 'cache:clear-all';
    protected $description = 'Limpa o cache de todas as conexões de banco de dados';

    public function handle()
    {
        $projects = DB::connection('mysql_monitor')->table('projects')->get();

        foreach ($projects as $project) {
            $databaseName = 'krayin_' . str_pad($project->id, 7, '0', STR_PAD_LEFT);

            Config::set('database.connections.mysql.database', $databaseName);

            $this->info("Limpando cache para o banco: {$databaseName}");

            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
        }

        $this->info('Cache limpo em todos os bancos!');
    }
}
