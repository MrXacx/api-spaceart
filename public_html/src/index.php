<?php

ini_set('display_errors', 0); // Deixa de exibir erros 
header('Access-Control-Allow-Origin: *'); // Libera o acesso à API
header('Accept: multipart/form-data'); // Define formato de entrada como form-data
header('Content-Type: application/json'); // Define retorno como json

require_once __DIR__.'/../../vendor/autoload.php'; // Carrega dependências

use App\Server;
use App\Util\Log;
use Monolog\Logger;
use Monolog\Level;

$_ENV = array_merge($_ENV, parse_ini_file('setup.ini', true));
$_ENV = array_merge($_ENV, $_ENV['DATABASE_DEVELOPMENT']) ;

Server::$logger = new Log(
    new Logger('SpaceartAPI'),
    Level::Debug
);

Server::$logger->build();

App\RoutesBuilder::build(); // Inicia rotas do servidor

$startTime = microtime(true);

use App\Controller\Tool\Controller;
use App\Util\Cache;

/**
 * Atenção!
 * Esta invocação substitui as rotas POST com final em '/delete' e '/update' 
 * por rotas DELETE e PUT com final em '' respectivamente.
 * 
 * Remover esta invocação pode resultar no mau funcionamento de rotas DELETE e PUT
 * caso o serviço de hospedagem não suporte-os nativamente.
 * 
 * Caso o suporte seja nativo, a remoção é encorajada.
 */
Server::replaceHTTPRequestForURI();
Server::$logger->build(); // Cria log para o dia

// Armazena endereço do client
Server::$logger->push('requisição feita por ' . Server::getClientIP(), Level::Info);

$response = new Symfony\Component\HttpFoundation\Response; // Inicia objeto de resposta
$response->headers->set('Content-Type', 'application/json'); // Define o formato da resposta

Controller::$cache = new Cache(
    // Inicializa manipulação de cache
    preg_replace('#[/?;=]#', '@', Server::getURI())
);

try {
    if (!Controller::$cache->isUsable()) { // Executa se o cache não existir ou estiver expirado

       Server::$logger->push('rotina de consulta ao banco de dados remoto inicializada', Level::Notice); // Informa consulta ao banco de dados
        $routes = new App\RoutesBuilder;
        $routes->fetchResponse($response, $routes->dispatch()); // Obtém a resposta da rota

    } else {
        Server::$logger->push('rotina de consulta ao cache inicializada', Level::Notice);
        $response->setContent(
            Controller::$cache->getContent()
        );
    }
    
} catch (Exception $ex) {
   Server::$logger->push('exceção lançada: ' . $ex->getMessage(), Level::Critical);
    $response->setStatusCode(500);
}

$response->send();

$time = microtime(true) - $startTime;

Server::$logger->push('resposta enviada para ' . Server::getClientIP(), Level::Info);
Server::$logger->push("tempo de resposta: $time ms", Level::Debug);

?>