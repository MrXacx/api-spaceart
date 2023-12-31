<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Tool\Controller;
use App\DAO\ArtistDB;
use App\DAO\EnterpriseDB;
use App\DAO\ReportDB;
use App\DAO\UsersDB;
use App\Model\Artist;
use App\Model\Enterprise;
use App\Model\Enumerate\AccountType;
use App\Model\Enumerate\ArtType;
use App\Model\Report;
use App\Model\Template\User;
use App\Util\Cache;
use App\Util\DataValidator;
use App\Util\Exception\UnexpectedHttpParameterException;
use \DateTime;
use Exception;

use Monolog\Level;

/**
 * Controlador de usuários e denúncia
 * 
 * @package Controller
 * @author Ariel Santos <MrXacx>
 * @author Marcos Vinícius <Mvini15>
 * @author Matheus Silva <theubr78>
 */
final class UserController
{
    use Controller;

    /**
     * Armazena usuário
     * @return// true caso o usuário seja criado
     */
    public function storeUser()
    {
        $user = false;
        $db = false;

        $type = $this->parameterList->getEnum('type', AccountType::class);
    
        switch ($type) {
            case AccountType::ARTIST:

                $user = new Artist;

                $user->setCPF($this->parameterList->getString('CPF'));
                $user->setArt(ArtType::tryFrom($this->parameterList->getString('art')));
                $user->setWage(floatval($this->parameterList->getString('wage')));
                $user->setBirthday(
                    DateTime::createFromFormat(ArtistDB::USUAL_DATE_FORMAT, $this->parameterList->getString('birthday'))
                );
                $db = new ArtistDB($user);
                break;

            case AccountType::ENTERPRISE:
                $user = new Enterprise;
                $user->setCNPJ($this->parameterList->getString('CNPJ'));
                $user->setNeighborhood($this->parameterList->getString('neighborhood'));
                $user->setAddress($this->parameterList->getString('address'));
                $user->setCompanyName($this->parameterList->getString('companyName'));
                $user->setSection($this->parameterList->getString('section'));

                $db = new EnterpriseDB($user);
                break;

            case null:
                UnexpectedHttpParameterException::throw(strval($type), 'TYPE ACCOUNT');
        }

        if ($user instanceof User && $db instanceof UsersDB) {

            $user->setName($this->parameterList->getString('name'));
            $user->setEmail($this->parameterList->getString('email'));
            $user->setPassword($this->parameterList->getString('password'));
            $user->setPhone($this->parameterList->getString('phone'));
            $user->setCEP($this->parameterList->getString('CEP'));
            $user->setState($this->parameterList->getString('state'));
            $user->setCity($this->parameterList->getString('city'));
            $user->setImage($this->parameterList->getString('image'));

            return $db->create();
        }


        return false;

    }

    /**
     * Obtém usuário
     * @return array dados de um usuário
     */
    public function getUser(): array
    {

        list($user, $db) = $this->getAccountType();
        $id = $this->parameterList->getString('id');


        // Caso o id seja o token de acesso, dados sigilosos serão consultados
        if ($this->parameterList->getBoolean('token')) { // Executa caso o usuário tenha passado um token pessoal no lugar do id
            $user->setID($id); // Inicia usuário com o id informado
            $user = $db->getUser();
        } else if (strlen($id) > 0) { // Executa caso o id público tenha sido informado
            // Note que o id tem preferência em comparação com o index

            $user->setID($id); // Inicia usuário com o id informado
            $user = $db->getPublicDataFromUserForID(); // Sobrescreve modelo user com modelo baseado no registro

        } else { // Executa caso o ide não tenha sido informado
            $user->setIndex($this->parameterList->getInt('index', -1)); // Inicia usuário com o index informado
            $user = $db->getPublicDataFromUserForIndex(); // Sobrescreve modelo user com modelo baseado no registro
        }

        $user = $this->filterNulls($user->toArray()); // Sobrescreve modelo com array sem elementos nulos
        Controller::$cache->create($user, Cache::MEDIUM_INTERVAL_STORAGE); // Guarda o cache
        return $user;

    }

    /**
     * Obtém dados de acesso ao sistema
     * @return array vetor com dados de acesso
     */
    public function getUserAcess(): array
    {
        /*
            No ato do login, o sistema servido deve possuir email e senha do usuário,
            mas pode não ter acesso ao id desse. Portanto, a API deve retornar apenas
            o id consultado com base nos dados informados.  
        */

        $user = new User;

        $user->setEmail($this->parameterList->getString('email'));
        $user->setPassword($this->parameterList->getString('password'));

        $db = new UsersDB($user);
        $db->updateTokenAcess(); // Gera novo token de acesso
        return $db->getAcess(); // Obtém dados de acesso

    }

    /**
     * Obtém lista de usuários
     * @return array
     */
    public function getUserList(): array
    {
        $offset = $this->fetchListOffset(); // Obtém posição de início da leitura
        $limit = $this->fetchListLimit(); // Obtém máximo de elementos da leitura

        list($user, $dao) = $this->getAccountType();
        \App\Server::$logger->push(json_encode(['FILTER' => $this->parameterList->getString('filter')]), Level::Critical);
        
        $list = match ($this->parameterList->getString('filter')) {
            'location' => $this->getRandomUserListByLocation($user, $dao, $limit),
            'art' => $this->getRandomUserListByArt($user, $dao, $limit),
            'name' => $this->getUserListByName($user, $dao, $offset, $limit),
            default => $dao->getList($offset, $limit)
        };

        $list = array_map(fn($user) => $this->filterNulls($user->toArray()), $list);
        Controller::$cache->create($list, Cache::LARGE_INTERVAL_STORAGE); // Armazena em cache
        return $list;
    }


    /**
     * Obtém lista randômica de usuários filtrados por cidade e estado
     * 
     * @param Artist|Enterprise $user Usuário
     * @param ArtistDB|EnterpriseDB $db Tabela do banco
     * @param int $limit Número máximo de itens esperados
     */
    private function getRandomUserListByLocation(Artist|Enterprise $user, ArtistDB|EnterpriseDB $db, int $limit): array
    {
        $user->setCity($this->parameterList->getString('city'));
        $user->setState($this->parameterList->getString('state'));
        return $db->getRandomListByLocation(0, $limit);
    }

    /**
     * Obtém lista randômica de usuários filtrados por tipo de arte
     * 
     * @param Artist|Enterprise $user Usuário
     * @param ArtistDB|EnterpriseDB $db Tabela do banco
     * @param int $limit Número máximo de itens esperados
     */
    private function getRandomUserListByArt(Artist $user, ArtistDB $db, int $limit): array
    {
        $user->setArt($this->parameterList->getEnum('art', ArtType::class));
        return $db->getRandomListByArt(0, $limit);
    }

    /**
     * Obtém lista de usuários filtrados pelo nome
     * 
     * @param Artist|Enterprise $user Usuário
     * @param ArtistDB|EnterpriseDB $db Tabela do banco
     * @param int $offset Posição inicial da consulta
     * @param int $limit Número máximo de itens esperados
     */
    private function getUserListByName(Artist|Enterprise $user, ArtistDB|EnterpriseDB $db, int $offset, int $limit): array
    {
        $name = $this->parameterList->getString('name');
        if ($name == '%') {
            UnexpectedHttpParameterException::throw('%', 'name');
        }
        $user->setName($name);
        $list = $db->getListByName($offset, $limit);

        Controller::$cache->create($list, 5);
        return $list;
    }

    /**
     * Obtém tipo de conta informado
     * @return array instância do modelo e do banco do tipo informado
     */
    private function getAccountType(): array
    {
        try{
            $type = $this->parameterList->getEnum('type', AccountType::class);
        } catch(Exception $e){
            $type = '';
        } finally {
            return match ($type) { // RECEBENDO O TIPO DA CONTA
                AccountType::ARTIST => [$artist = new Artist(), new ArtistDB($artist)],
                AccountType::ENTERPRISE => [$enterprise = new Enterprise(), new EnterpriseDB($enterprise)],
                default => [$user = new User(), new UsersDB($user)],
            };
        }
    }

    /**
     * Atualiza atributo do usuário
     * @return true caso o dado tenha sido atualizado
     */
    public function updateUser(): bool
    {

        $column = ($this->parameterList->getString('column')); // RECEBE A COLUNA QUE SERÁ ALTERADA
        $info = ($this->parameterList->getString('info')); // RECEBE A INFORMAÇÃO QUE ELE DESEJA ALTERAR DE ACORDO COM A CONTA EM QUE ESTÁ CADASTRADO O ID

        if ($column == 'companyName') { // Executa caso o camel case tenha sido uitilzada para razão social
            $column = EnterpriseDB::COMPANY_NAME;
        }

        $validator = new DataValidator;
        if ($validator->isValidToFlag($info, $column)) {

            if (UsersDB::isEditalbeColumn($column)) {
                $user = new User;
                $db = new UsersDB($user);
            } else if (ArtistDB::isEditalbeColumn($column)) {
                $user = new Artist;
                $db = new ArtistDB($user);
            } else if (EnterpriseDB::isEditalbeColumn($column)) {
                $user = new Enterprise;
                $db = new EnterpriseDB($user);
            } else {
                return false;
            }

            // Define ID do usuário
            $user->setID($this->parameterList->getString('id')); // PASSA O ID DO USUARIO PARA O MODELO
            return $db->update($column, $info); //RETORNA SE ALTEROU OU NÃO, DE ACORDO COM A VERIFICAÇÃO DO IF
        }
        return false; // RETORNA FALSO CASO NÃO TENHA PASSADO DA VERIFICAÇÃO
    }

    /**
     * Deleta usuário
     * @return true caso o usuário tenha sido deletado
     */
    public function deleteUser(): bool
    {

        $user = new User; //MODELO DE USUÁRIO
        $user->setID($this->parameterList->getString('id')); //PASSA O ID DE USUÁRIO PARA O MODELO

        $db = new UsersDB($user); //LIGA O BANCO
        return $db->delete(); // RETORNA SE DELETOU OU NÃO

    }

    /**
     * Armazena denúncia
     * @return bool true se a denúncia foi armazenada
     */
    public function storeReport(): bool
    {
        $report = new Report($this->parameterList->getString('reporter'));
        $report->setReported($this->parameterList->getString('reported'));
        $report->setReason($this->parameterList->getString('reason'));

        $db = new ReportDB($report);
        return $db->create();
    }

    /**
     * Obtém denúncia de um usuário
     * @return array todos os dados da denúncia
     */
    public function getReport(): array
    {
        $report = new Report($this->parameterList->getString('reporter'));
        $report->setID($this->parameterList->getString('id'));

        $db = new ReportDB($report);
        $report = $db->getReport()->toArray();
        Controller::$cache->create($report, Cache::LARGE_INTERVAL_STORAGE);
        return $report;
    }

    /**
     * Obtém lista de denúncias
     * @return array lista com dados das denúncias de um usuário
     */
    public function getReportList(): array
    {
        $offset = $this->fetchListOffset(); // Obtém posição de início da leitura
        $limit = $this->fetchListLimit(); // Obtém máximo de elementos da leitura

        $db = new ReportDB(
            new Report(
                $this->parameterList->getString('reporter')
            )
        );
        $list = array_map(fn($report) => $report->toArray(), $db->getList($offset, $limit));
        Controller::$cache->create($list, Cache::LARGE_INTERVAL_STORAGE);
        return $list;

    }

    public function getUserStats(): array{
        try{
            $type = $this->parameterList->getEnum('type', AccountType::class);   
            $db = $type == AccountType::ARTIST ? new ArtistDB : new EnterpriseDB;
            return $db->getStats();
        } catch(Exception $e){
            throw new UnexpectedHttpParameterException("É necessário apresentar um tipo de conta");
        } 
    }
}

?>
