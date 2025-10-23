<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class DatabaseConfigProvider extends ServiceProvider
{
    /**
     * Registre quaisquer serviços de aplicativos.
     *
     * @return void
     */
    public function register()
    {
        // Nenhuma ação necessária no registro
    }

    /**
     * Inicializa a configuração do banco de dados com base na URL validada.
     *
     * @return void
     */
    public function boot()
    {
        $middleware = new \App\Http\Middleware\CheckUrlInMonitor;
        $middleware->handle(request(), function () {
            return response('OK');
        });
        // exit();

        // Se estiver rodando no CLI (terminal), não executa a validação
        if (App::runningInConsole()) {
            return;
        }

        // Verifica se a URL foi validada no middleware
        if (Session::get('url_validated')) {
            // Consultar o banco mysql_monitor para obter o ID do projeto
            $project = DB::connection('mysql_monitor')->table('projects')
                ->where('url', request()->getHost()) // Pegando o domínio atual
                ->first();

            // Verifica se encontrou o projeto e possui ID
            if ($project && isset($project->id)) {
                // Formata o nome do banco conforme a regra
                $databaseName = 'krayin_'.str_pad($project->id, 7, '0', STR_PAD_LEFT);
                // $databaseName = 'krayin_0base';

                // Define apenas o nome do banco, mantendo usuário e senha padrão do projeto
                Config::set('database.connections.mysql.database', $databaseName);
            } else {
                // Se não encontrar, podemos exibir uma mensagem e encerrar a execução
                print_r('Erro: XPTO d0120391'); // Configuração do banco de dados não encontrada.
                exit();
            }
        } else {
            // URL não validada, impedir o carregamento
            print_r('Erro: XPTO 3120hjd0'); // URL não validada, configuração do banco de dados não será carregada.
            exit();
        }
    }
}
