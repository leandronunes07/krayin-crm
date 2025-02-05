<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class DatabaseConfigProvider extends ServiceProvider
{
    /**
     * Registre quaisquer serviços de aplicativos.
     *
     * @return void
     */
    public function register()
    {
        // Não precisamos fazer nada aqui para esse caso
    }

    /**
     * Inicialize qualquer coisa no início da execução.
     *
     * @return void
     */
    public function boot()
    {
        // Verifica se a URL foi validada no middleware
        if (Session::get('url_validated')) {
            // Consultar o banco de dados mysql_monitor para pegar as configurações do banco
            $databaseConfig = DB::connection('mysql_monitor')->table('db_config_table') // Tabela que armazena as configurações do banco
                ->where('config_key', 'db_connection')
                ->first();

            // Verifique se a configuração foi encontrada
            if ($databaseConfig) {
                // Preencher a configuração do banco de dados com os valores obtidos
                Config::set('database.connections.mysql.host', $databaseConfig->host);
                Config::set('database.connections.mysql.port', $databaseConfig->port);
                Config::set('database.connections.mysql.database', $databaseConfig->database);
                Config::set('database.connections.mysql.username', $databaseConfig->username);
                Config::set('database.connections.mysql.password', $databaseConfig->password);
            } else {
                // Se não encontrar, você pode definir valores padrões ou lançar uma exceção
                //\Log::error('Configuração do banco de dados não encontrada.');
            }
        } else {
            // URL não validada, podemos interromper a configuração ou realizar outra ação
            //\Log::warning('URL não validada, configuração do banco de dados não será carregada.');
        }
    }
}
