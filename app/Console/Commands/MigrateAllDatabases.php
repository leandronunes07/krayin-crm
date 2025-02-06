<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class MigrateAllDatabases extends Command
{
    protected $signature = 'migrate:all';
    protected $description = 'Run migrations on all databases';

    public function handle()
    {
        // Obtém todos os bancos de dados do sistema
        $databases = DB::connection('mysql_monitor')->table('projects')->pluck('id');

        foreach ($databases as $dbId) {
            // Gera o nome do banco de dados conforme a regra do seu projeto
            $databaseName = 'krayin_' . str_pad($dbId, 7, '0', STR_PAD_LEFT);

            // Define a conexão temporária para o banco específico
            Config::set('database.connections.mysql.database', $databaseName);
            DB::purge('mysql'); // Limpa a conexão para garantir que a mudança seja aplicada

            $this->info("Rodando migrations para o banco: $databaseName");

            // Executa a migration no banco de dados atual
            Artisan::call('migrate', ['--force' => true]);

            $this->info(Artisan::output());
        }

        $this->info("Migrations concluídas para todos os bancos.");
    }
}
