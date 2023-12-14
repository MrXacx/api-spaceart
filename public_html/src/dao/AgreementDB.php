<?php

declare(strict_types=1);

namespace App\DAO;

use App\DAO\Template\DatabaseAcess;
use App\Model\Agreement;

/**
 * Classe de maniupulação da tabela Agreements
 * 
 * @package DAO
 * @author Ariel Santos <MrXacx>
 */
class AgreementDB extends DatabaseAcess
{

    public const HIRER = 'hirer';
    public const HIRED = 'hired';
    public const PRICE = 'price';
    public const ART = 'art';
    public const DATE = 'date';
    public const START_TIME = 'start_time';
    public const END_TIME = 'end_time';
    public const STATUS = 'status';
    public const DESCRIPTION = 'description';

    /**
     * Modelo de contrato a ser utilizado na manipulação
     * @var Agreement
     */
    private Agreement $agreement;

    /**
     * @param Agreement $agreement Modelo de contrato a ser utilizado na manipulação
     */
    function __construct(Agreement $agreement)
    {
        $this->agreement = $agreement;
        parent::__construct();
    }

    public static function isEditalbeColumn(string $column): bool
    {
        return is_int(
            array_search($column, [
                self::STATUS,
                self::PRICE,
                self::DATE,
                self::START_TIME,
                self::END_TIME,
            ])
        );
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function create(): bool
    {

        // Passa query SQL de criação
        $query = $this->getConnection()->prepare('INSERT INTO agreement (id, hirer, hired, price, date, start_time, end_time, art, description) VALUES (UUID(),?,?,?,?,?,?,?,?)');

        // Substitui interrogações pelos valores dos atributos
        $query->bindValue(1, $this->agreement->getHirer());
        $query->bindValue(2, $this->agreement->getHired());
        $query->bindValue(3, $this->agreement->getPrice());
        $query->bindValue(4, $this->agreement->getDate()->format(parent::DB_DATE_FORMAT));

        $time = $this->agreement->getTime();
        $query->bindValue(5, $time['start']->format(parent::DB_TIME_FORMAT));
        $query->bindValue(6, $time['end']->format(parent::DB_TIME_FORMAT));
        $query->bindValue(7, $this->agreement->getArt()->value);
        $query->bindValue(8, $this->agreement->getDescription());

        return $query->execute();
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function getList(int $offset = 0, int $limit = 10): array
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare("SELECT * FROM agreement WHERE hirer = ? OR hired = ? ORDER BY ABS(DATEDIFF(date, CURDATE())) LIMIT $limit OFFSET $offset");


        $query->bindValue(1, $this->agreement->gethirer());
        $query->bindValue(2, $this->agreement->gethired());

        if ($query->execute()) { // Executa se consulta não falhar
            return array_map(fn($agreement) => Agreement::getInstanceOf($agreement), $this->fetchRecord($query));
        }

        // Executa em caso de falhas esperadas
        throw new \RuntimeException('Operação falhou!');
    }

    /**
     * Obtém modelo de contrato
     * @return Agreement modelo de contrato
     */
    public function getAgreement(): Agreement
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare('SELECT * FROM agreement WHERE id = ?');
        $query->bindValue(1, $this->agreement->getID()); // Substitui interrogação na query pelo ID passado

        if ($query->execute()) { // Executa se a query for aceita
            return Agreement::getInstanceOf($this->fetchRecord($query, false));
        }

        // Executa em caso de falhas esperadas
        throw new \RuntimeException('Operação falhou!');
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function update(string $column, string $value): bool
    {
        // Passa query SQL de atualização
        $query = $this->getConnection()->prepare("UPDATE agreement SET $column = ? WHERE id = ?");

        // Substitui interrogações pelos valores das variáveis
        $query->bindValue(1, $value);
        $query->bindValue(2, $this->agreement->getID());

        return $query->execute();
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function delete(): bool
    {
        // Deleta seleção do banco
        $query = $this->getConnection()->prepare('DELETE FROM agreement WHERE id = ?');
        $query->bindValue(1, $this->agreement->getID());
        return $query->execute();
    }

    public function getStats(): array{
        $query = $this->getConnection()
        ->prepare(
            "
                SELECT u.id AS 'user', status, COUNT(*) AS total
                FROM agreement AS a
                INNER JOIN users AS u
                ON u.id IN (a.hirer, a.hired)
                GROUP BY u.id, a.status
                HAVING u.id = ?
            "
        );

        $query->bindValue(1, $this->agreement->getID());

        if($query->execute()){
            return $this->fetchRecord($query, false);
        }

        throw new \RuntimeException('Operação falhou!');   
    }
}