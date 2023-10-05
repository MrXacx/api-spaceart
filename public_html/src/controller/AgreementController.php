<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Tool\Controller;
use App\DAO\AgreementDB;
use App\DAO\RateDB;
use App\Model\Agreement;
use App\Model\Enumerate\ArtType;
use App\Model\Rate;
use App\Util\Cache;
use App\Util\DataValidator;
use DateTime;

/**
 * Controlador de contrato e avaliações
 * 
 * @package Controller
 * @author Ariel Santos <MrXacx>
 * @author Marcos Vinícius <Mvini15>
 * @author Matheus Silva <theubr78>
 */
final class AgreementController
{
    use Controller;

    /**
     * Armazena um contrato
     * @return bool true caso a operação funcione corretamente
     */
    public function storeAgreement(): bool
    {

        $agreement = new Agreement; // Inicia modelo

        $agreement->setHirer($this->parameterList->getString('hirer')); // obtém id do contratante
        $agreement->setHired($this->parameterList->getString('hired')); // obtém id do contratado
        $agreement->setDescription($this->parameterList->getString('description')); // obtém id do contratado
        $agreement->setArt($this->parameterList->getEnum('art', ArtType::class)); // obtém tipo de arte
        $agreement->setPrice(floatval($this->parameterList->getString('price')));

        $agreement->setDate(DateTime::createFromFormat(AgreementDB::USUAL_DATE_FORMAT, $this->parameterList->getString('date'))); // obtém data do evento

        $time = explode(';', $this->parameterList->getString('time'));
        $agreement->setTime(
            // obtém horários de início e fim do evento
            DateTime::createFromFormat(AgreementDB::USUAL_TIME_FORMAT, $time[0]),
            DateTime::createFromFormat(AgreementDB::USUAL_TIME_FORMAT, $time[1])
        );

        $db = new AgreementDB($agreement); // inicia banco com modelo de contrato
        return $db->create(); // armazena registro

    }

    /**
     * Obtém dados de um contrato em específico
     * @return array Todos os dados de um chat em específico
     */
    public function getAgreement(): array
    {
        $agreement = new Agreement;
        $agreement->setID($this->parameterList->getString('id')); // Obtém id informado

        $db = new AgreementDB($agreement); // Inicia objeto para manipular o chat
        $agreement = $this->filterNulls($db->getAgreement()->toArray());

        Controller::$cache->create($agreement, Cache::TINY_INTERVAL_STORAGE);
        return $agreement;

    }

    /**
     * Obtém lista de contratos
     * @return array
     */
    public function getAgreementList(): array
    {

        $offset = $this->fetchListOffset(); // Obtém posição de início da leitura
        $limit = $this->fetchListLimit(); // Obtém máximo de elementos da leitura

        $agreement = new Agreement;
        $agreement->setHirer($this->parameterList->getString('user'));
        $agreement->setHired($this->parameterList->getString('user'));
        $db = new AgreementDB($agreement);
        $list = array_map(fn($agreement) => $this->filterNulls($agreement->toArray()), $db->getList($offset, $limit));

        Controller::$cache->create($list, Cache::MEDIUM_INTERVAL_STORAGE);
        return $list;
    }

    /**
     * Atualiza dado de um contrato
     */
    public function updateAgreement(): bool
    {

        $column = ($this->parameterList->getString('column')); // RECEBE A COLUNA QUE SERÁ ALTERADA
        $info = ($this->parameterList->getString('info')); // RECEBE A INFORMAÇÃO QUE ELE DESEJA ALTERAR DE ACORDO COM A CONTA EM QUE ESTÁ CADASTRADO O ID

        $validator = new DataValidator;
        if ($validator->isValidToFlag($info, $column) && AgreementDB::isEditalbeColumn($column)) {

            $agreement = new Agreement; // INICIANDO MODELO DO CONTRATO 
            // Define ID do usuário
            $agreement->setID($this->parameterList->getString('id')); // PASSA O ID DO USUARIO PARA O MODELO

            return (new AgreementDB($agreement))->update($column, $info); //RETORNA SE ALTEROU OU NÃO, DE ACORDO COM A VERIFICAÇÃO DO IF
        }
        return false; // RETORNA FALSO CASO NÃO TENHA PASSADO DA VERIFICAÇÃO
    }

    /**
     * Deleta contrato
     * @return bool true caso a operação funcione corretamente
     */
    public function deleteAgreement(): bool
    {
        $agreement = new Agreement;
        $agreement->setID($this->parameterList->getString('id')); // obtém id do contrato

        $db = new AgreementDB($agreement); // inicia banco com modelo de contrato
        return $db->delete(); // deleta contrato
    }

    /**
     * Armazena avalização
     */
    public function storeRate(): bool
    {
        $rate = new Rate($this->parameterList->getString('agreement')); // inicia modelo de avaliação
        $rate->setAuthor($this->parameterList->getString('author')); // obtém autor
        $rate->setDescription($this->parameterList->getString('description')); // obtém descrição da avaliação
        $rate->setRate(floatval($this->parameterList->getString('rate'))); // obtém nota

        $db = new RateDB($rate); // inicia banco
        return $db->create(); // armazena avaliação
    }

    /**
     * Obtém dados de uma avaliação
     */
    public function getRate(): array
    {
        $rate = new Rate($this->parameterList->getString('agreement'));
        $rate->setAuthor($this->parameterList->getString('author')); // Obtém id informado

        $db = new RateDB($rate); // Inicia objeto para manipular o chat
        $rate = $this->filterNulls($db->getRate()->toArray());

        Controller::$cache->create($rate, Cache::TINY_INTERVAL_STORAGE);
        return $rate;
    }

    /**
     * Obtém lista de avaliações de um contrato
     */
    public function getRateList(): array
    {

        $offset = $this->fetchListOffset(); // Obtém posição de início da leitura
        $limit = $this->fetchListLimit(); // Obtém máximo de elementos da leitura

        $rate = new Rate($this->parameterList->getString('agreement'));

        $db = new RateDB($rate); // Inicia objeto para manipular o chat
        $list = array_map(fn($rate) => $this->filterNulls($rate->toArray()), $db->getList($offset, $limit));

        Controller::$cache->create($list, Cache::TINY_INTERVAL_STORAGE);
        return $list;
    }

    /**
     * Atualiza dados de uma avaliação
     */
    public function updateRate(): bool
    {

        $column = ($this->parameterList->getString('column')); // RECEBE A COLUNA QUE SERÁ ALTERADA
        $info = $this->parameterList->getString('info'); // RECEBE A INFORMAÇÃO QUE ELE DESEJA ALTERAR DE ACORDO COM A CONTA EM QUE ESTÁ CADASTRADO O ID

        $validator = new DataValidator;
        if ($validator->isValidToFlag($info, $column) && RateDB::isEditalbeColumn($column)) {

            $rate = new Rate($this->parameterList->getString('agreement')); // INICIANDO MODELO DO USUÁRIO 
            $rate->setAuthor($this->parameterList->getString('author')); // PASSA O ID DO AUTOR

            return (new RateDB($rate))->update($column, $info); //RETORNA SE ALTEROU OU NÃO, DE ACORDO COM A VERIFICAÇÃO DO IF
        }
        return false; // RETORNA FALSO CASO NÃO TENHA PASSADO DA VERIFICAÇÃO
    }

    /**
     * Deleta avaliação
     */
    public function deleteRate(): bool
    {
        $rate = new Rate($this->parameterList->getString('agreement')); // inicia modelo de avaliação
        $rate->setAuthor($this->parameterList->getString('author')); // obtém id da avaliação

        $db = new RateDB($rate); // inicia banco
        return $db->delete(); // deleta avaliação
    }

}

?>