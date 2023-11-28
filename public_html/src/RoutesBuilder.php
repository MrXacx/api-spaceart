<?php

declare(strict_types=1);

namespace App;

use App\Controller\AgreementController;
use App\Controller\ChatController;
use App\Controller\PostController;
use App\Controller\SelectionController;
use App\Controller\UserController;
use App\Server;
use App\Util\Exception\DatabaseException;
use App\Util\Exception\NoReturnRetrieveException;
use App\Util\Exception\Template\InvalidAttributeFormatException;
use App\Util\Exception\UnexpectedHttpParameterException;
use FastRoute;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Monolog\Level;
use PDOException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Classe para controlar rotas
 * @package App
 * @author Ariel Santos <MrXacx>
 */
class RoutesBuilder
{
    private static Dispatcher $dispatcher;

    /**
     * Inicia as rotas da API
     */
    public static function build(): void
    {

        static::$dispatcher = FastRoute\simpleDispatcher(
            function (RouteCollector $collector) { // Inicia rotas
    
                $collector->post('', UserController::class . '@storeUser'); // Busca id do usuário
    

                $collector->addGroup('/user', function (RouteCollector $collector) // rotas com início "/user"
                {
                    $collector->get('', UserController::class . '@getUser'); // Busca todos os dados de um usuário
                    $collector->get('/list', UserController::class . '@getUserList'); // Busca lista de usuários
                    $collector->get('/sign-in', UserController::class . '@getUserAcess'); // Busca id do usuário  
                    $collector->post('', UserController::class . '@storeUser'); // Busca id do usuário  
                    $collector->put('', UserController::class . '@updateUser'); // Busca id do usuário     
                    $collector->delete('', UserController::class . '@deleteUser'); // Deleta usuário
    
                    $collector->addGroup('/report', function (RouteCollector $collector) // rotas com início "/user/report"
                    {
                        $collector->get('', UserController::class . '@getReport'); // Retorna uma denúncia
                        $collector->post('', UserController::class . '@storeReport'); // Armazena uma denúncia
                        $collector->get('/list', UserController::class . '@getReportList'); // Retorna uma lista de denúncias
    
                    });
                });

                $collector->addGroup('/chat', function (RouteCollector $collector) // rotas com início "/chat"
                {
                    $collector->get('', ChatController::class . '@getChat'); // Exibe um chat
                    $collector->post('', ChatController::class . '@storeChat'); // Cria um chat
                    $collector->get('/list', ChatController::class . '@getChatList'); // Exibe lista de chats
    
                    $collector->addGroup('/message', function (RouteCollector $collector) // rotas com início "/chat/message"
                    {
                        $collector->get('', ChatController::class . '@getMessage'); //Abre a conversa selecionada
                        $collector->post('', ChatController::class . '@storeMessage'); //Cria um chat novo
                        $collector->delete('', ChatController::class . '@deleteMessage'); //Deleta um chat existente
                        $collector->get('/list', ChatController::class . '@getMessageList'); //Abre a conversa selecionada
                    });
                });

                $collector->addGroup('/agreement', function (RouteCollector $collector) //rotas com início '/agreement'
                {

                    $collector->get('', AgreementController::class . '@getAgreement'); // Exibe dados de um contrato
                    $collector->post('', AgreementController::class . '@storeAgreement'); // Cria um contrato
                    $collector->put('', AgreementController::class . '@updateAgreement'); // Atualiza as informações do contrato
                    $collector->delete('', AgreementController::class . '@deleteAgreement'); // Deleta um contrato
                    $collector->get('/list', AgreementController::class . '@getAgreementList'); // Exibe lista de contratos
    
                    $collector->addGroup('/rate', function (RouteCollector $collector) //rotas com início '/agreement/rate'
                    {
                        $collector->get('', AgreementController::class . '@getRate'); // Exibe uma avaliação
                        $collector->post('', AgreementController::class . '@storeRate'); // Cria uma nova avaliação
                        $collector->put('', AgreementController::class . '@updateRate'); // Atualiza as informações da avaliação
                        $collector->delete('', AgreementController::class . '@deleteRate'); // Deleta a avaliação
                        $collector->get('/list', AgreementController::class . '@getRateList'); // Exibe lista de avaliações de um contrato
                    });

                });

                $collector->addGroup('/selection', function (RouteCollector $collector) //rotas com início '/selection'
                {
                    $collector->get('', SelectionController::class . '@getSelection'); //Abre uma vaga em específico
                    $collector->post('', SelectionController::class . '@storeSelection'); //Cria uma vaga
                    $collector->put('', SelectionController::class . '@updateSelection'); //Atualiza as informações de uma vaga
                    $collector->delete('', SelectionController::class . '@deleteSelection'); //Deleta uma vaga
                    $collector->get('/list', SelectionController::class . '@getSelectionList'); //Abre o menu de criação de vaga
    
                    $collector->addGroup('/application', function (RouteCollector $collector) //rotas com início '/selection/application'
                    {
                        $collector->get('', SelectionController::class . '@getApplication'); //Abre uma vaga em específico
                        $collector->post('', SelectionController::class . '@storeApplication'); //Cria uma candidatura
                        $collector->delete('', SelectionController::class . '@deleteApplication'); //Cria uma candidatura
                        $collector->get('/list', SelectionController::class . '@getApplicationList'); //Abre o menu de candidatura
    
                    });
                });

                $collector->addGroup('/post', function (RouteCollector $collector) //rotas com início '/selection/application'
                {
                    $collector->get('', PostController::class . '@getPost'); //Abre uma vaga em específico
                    $collector->post('', PostController::class . '@storePost'); //Cria uma candidatura
                    $collector->delete('', PostController::class . '@deletePost'); //Cria uma candidatura
                    $collector->get('/list', PostController::class . '@getPostList'); //Abre o menu de candidatura
    
                });
            }
        );

    }

    /**
     * Obtém dados da rota executada
     * @return array vetor com todas as informações disponíveis da rota
     */
    public function dispatch(): array
    {
        return static::$dispatcher->dispatch(Server::getHTTPMethod(), Server::getStrippedURI());
    }

    /**
     * Busca resposta para a rota executada
     * @param Response Objeto manipulador de resposta
     * @param array informações da rota
     */
    public function fetchResponse(Response $responseHandler, array $fetchParams)
    {

        //Busca callback para o status da requisição
        switch ($fetchParams[0]) {

            case Dispatcher::NOT_FOUND: // Caso a rota seja desconhecida
                Server::$logger->push(
                    'a rota ' . Server::getStrippedURI() . ' não foi localizada',
                    Level::Info
                );
                $responseHandler->setStatusCode(Response::HTTP_NOT_FOUND);

                return;
            case Dispatcher::METHOD_NOT_ALLOWED: // Caso a rota seja conhecida, mas não esteja esperando o método utilizado
                Server::$logger->push(
                    'tentativa falha de utilizar o método ' . Server::getHTTPMethod() . ' na rota ' . Server::getStrippedURI(),
                    Level::Info
                );
                $responseHandler->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
                return;

            case Dispatcher::FOUND: // Caso a rota esteja correta
                Server::$logger->push(
                    'a rota ' . Server::getStrippedURI() . ' foi acionada pelo método ' . Server::getHTTPMethod(),
                    Level::Debug
                );

                list($status, $handler, $vars) = $fetchParams; // Obtém manipulador da rota e parâmetros de manipulação
                list($class, $method) = explode('@', $handler); // Obtém classe e método a ser executado

                try {
                    $status = Response::HTTP_ACCEPTED;

                    try {
                        $content = call_user_func_array([new $class, $method], $vars); // Instancia classe e chama o método passando os parâmetros retornados pela rota
                        $status = $this->handleResponse($responseHandler, $content);
                    } catch (PDOException $ex) {
                        DatabaseException::throw($ex->getMessage());
                    }

                } catch (InvalidAttributeFormatException $ex) {

                    Server::$logger->push($ex->getMessage(), Level::Info);
                    $status = Response::HTTP_BAD_REQUEST;
                    $responseHandler->setContent(json_encode($ex->getMessage()));

                } catch (NoReturnRetrieveException $ex) {
                    $responseHandler->setContent(json_encode([]));
                } catch (DatabaseException $ex) {
                    $message = $ex->getMessage(); // Obtém mensagem da exceção
                    $startpoint = strpos($message, '[') + 1; // Posição inicial do código sqlstate
                    $code = substr(
                        $message,
                        $startpoint,
                        strpos($message, ']') - $startpoint
                    ); //  Extrai SQLSTATE da mensagem de exceção

                    switch ($code) {
                        case 23000:
                        case 22001: // Character data, right truncation occurred
                        case 22002: // A null value, or the absence of an indicator parameter was detected
                        case 22004: // A null value is not allowed.
                        case 22007: // An invalid datetime format was detected
                        case 23502: // An insert or update value is null, but the column cannot contain null values.
                        case 23503: // The insert or update value of a foreign key is invalid.
                            $responseHandler->setContent(json_encode($message));

                        case 54001: // The statement is too long or too complex.
                            $level = Level::Alert;
                            break;

                        case '08001': // The connection was unable to be established to the application server or other server.
                        case '08003': // The connection does not exist.
                        case '08004': // The application server rejected establishment of the connection.
                        case '42505': // Connection authorization failure occurred.
                        case '26501': // The statement identified does not exist.
                            $level = Level::Emergency;
                            break;

                        default:
                            $level = Level::Critical;
                            $responseHandler->setContent(json_encode($message));
                    }

                    Server::$logger->push($ex->getMessage(), $level);
                    $status = Response::HTTP_BAD_REQUEST;

                } catch (UnexpectedHttpParameterException $ex) {

                    Server::$logger->push('parâmetro HTTP ' . $ex->getMessage() . ' não condiz com as opções', Level::Debug);
                    $status = Response::HTTP_BAD_REQUEST;

                } catch (Throwable $ex) {

                    Server::$logger->push(
                        'exceção lançada: ' . $ex->getMessage()
                        . ' em ' . $ex->getFile()
                        . ' linha ' . $ex->getLine()
                        ,
                        Level::Critical
                    );
                    $responseHandler->setContent(json_encode($ex->getMessage()));
                    $status = Response::HTTP_INTERNAL_SERVER_ERROR;

                } finally {
                    $responseHandler->setStatusCode($status);
                }
        }
    }

    private function handleResponse(Response $responseHandler, mixed $content): int
    {
        if ($content === true) { // Executa caso o retorno seja true
            return match (Server::getHTTPMethod()) { // Obtém código HTTP adequado
                'DELETE', 'PUT' => Response::HTTP_NO_CONTENT, // Funcionou, mas não retorna dados
                'POST' => Response::HTTP_CREATED, // Novo recurso disponível
                'GET' => Response::HTTP_OK, // Funcionou
                default => Response::HTTP_INTERNAL_SERVER_ERROR,
            };

        } else if (is_array($content)) { // Executa caso o conteúdo obtido seja um vetor

            $responseHandler->setContent(json_encode($content, JSON_INVALID_UTF8_IGNORE)); // Define conteúdo a ser repondido ao cliente
            return Response::HTTP_OK; // Funcionou e retorna conteúdo
        }

        return Response::HTTP_BAD_REQUEST; // Erro na requisição

    }

}
?>
