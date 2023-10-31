<?php

declare(strict_types=1);

namespace App\DAO\Template;

use App\Util\Exception\NoReturnRetrieveException;
use PDO;
use PDOException;
use PDOStatement;
use ReflectionClassConstant;
use App\Server;
use Monolog\Level;

/**
 * Classe de conexão com o banco de dados
 * @package DAO
 * @author Ariel Santos <MrXacx>
 */
abstract class DatabaseAcess
{
    /**
     * Objeto de conexão com o banco
     * @var PDO
     */
    private PDO $connection;

    public const DB_TIMESTAMP_FORMAT = 'Y-m-d H:i:s';
    public const USUAL_TIMESTAMP_FORMAT = 'd/m/Y H:i:s';
    public const DB_DATE_FORMAT = 'Y-m-d';
    public const USUAL_DATE_FORMAT = 'd/m/Y';
    public const DB_TIME_FORMAT = 'H:i:s';
    public const USUAL_TIME_FORMAT = 'H:i';


    function __construct()
    {
        try {
            $this->connection = new PDO($_ENV['db_host'], $_ENV['db_user'], $_ENV['db_pwd']);
            Server::$logger->push('conexão com banco foi iniciada', Level::Notice);
        } catch (\Exception $ex) {
            Server::$logger->push('conexão com banco falhou', Level::Critical);
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * Obtém objeto de manipulação do banco
     * @return PDO Manipulador do banco de dados
     * 
     */
    final protected function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Obtém uuid
     * @return string Sequência aleatória de 36 dígitos
     * 
     */
    final protected function getRandomID(): string
    {
        return \Ramsey\Uuid\Uuid::uuid7()->toString();
    }

    /**
     * Obtém valor consultado no banco de dados
     * @param PDOStatement $query Objeto de consulta à tabela
     * @param bool $multipleRecords Parâmetro de controle de múltiplos registros esperados
     * @return array Valor buscado no banco
     * @throws PDOException Caso valor retornado seja de um tipo diferente de array ou string
     */
    final protected function fetchRecord(PDOStatement $query, bool $multipleRecords = true): array
    {
        $response = $multipleRecords ? $query->fetchAll(PDO::FETCH_ASSOC) : $query->fetch(PDO::FETCH_ASSOC);
        if (is_array($response)) {
            return $response;
        }

        NoReturnRetrieveException::throw();
    }

    abstract public static function isEditalbeColumn(string $column): bool;

    function __destruct()
    {
        unset($this->connection);
        Server::$logger->push('conexão com banco foi encerrada', Level::Notice);
    }

    /**
     * Insere linhs na tabela
     * 
     * @return int Número de linhas afetadas
     * @throws \RuntimeException Falha causada pela conexão com o banco de dados
     */
    abstract public function create(): bool;

    /**
     * Obtém lista de dados não sensíveis da entidade
     * 
     * @param int $offset Linha de início da consulta 
     * @param int $limit Quantidade de registros a ser retornada
     * @return array Lista de registros
     */
    abstract public function getList(int $offset = 0, int $limit = 10): array;

    /**
     * Atualiza determinada célula do banco
     * 
     * @param string $column Nome da coluna que deve sofrer alterações
     * @param string $value Novo valor da coluna
     * @return int Número de linhas afetadas
     */
    abstract public function update(string $column, string $value): bool;

    /**
     * Deleta linha do banco
     * 
     * @return bool se a operação foi executada
     * @throws \RuntimeException Falha causada pela conexão com o banco de dados
     */
    abstract public function delete(): bool;
}