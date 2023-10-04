<?php

declare(strict_types=1);

namespace App\DAO;

use App\DAO\Template\DatabaseAcess;
use App\Model\Report;
use App\Model\Template\User;
use RuntimeException;

/**
 * Classe de maniupulação da tabela Reports
 * 
 * @package DAO
 * @author Ariel Santos (MrXacx)
 */
class ReportDB extends DatabaseAcess
{
    public const REPORTER = 'reporter';
    public const REPORTED = 'reported';
    public const REASON = 'reason';
    public const ACCEPTED = 'accepted';

    /**
     * Modelo de candidatura a ser manipulado
     * @var Report
     */
    private Report $report;

    /**
     * @param Report $report Modelo de candidatura a ser manipulado
     */
    function __construct(Report $report)
    {
        $this->report = $report;
        parent::__construct();
    }

    public static function isEditalbeColumn(string $column): bool
    {
        return false;
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function create(): bool
    {

        $this->report->setID($this->getRandomID()); // Gera uuid

        // Passa query SQL de criação
        $query = $this->getConnection()->prepare('INSERT INTO report (id, reporter, reported, reason) VALUES (UUID(),?,?,?)');

        // Substitui interrogações pelos valores dos atributos
        $query->bindValue(1, $this->report->getReporter());
        $query->bindValue(2, $this->report->getReported());
        $query->bindValue(3, $this->report->getReason());

        return $query->execute();
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function getList(int $offset = 0, int $limit = 10): array
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare("SELECT * FROM report WHERE reporter = ? LIMIT $limit OFFSET $offset");
        $query->bindValue(1, $this->report->getReporter()); // Substitui interrogação na query pelo ID passado

        if ($query->execute()) { // Executa se consulta não falhar
            return array_map(fn($report) => Report::getInstanceOf($report), $this->fetchRecord($query));
        }

        // Executa em caso de falhas esperadas
        throw new RuntimeException('Operação falhou!');
    }

    public function getReport(): Report
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare("SELECT * FROM report WHERE id = ?");
        $query->bindValue(1, $this->report->getID()); // Substitui interrogação na query pelo ID passado

        if ($query->execute()) { // Executa se consulta não falhar
            return Report::getInstanceOf($this->fetchRecord($query, false));
        }

        // Executa em caso de falhas esperadas
        throw new RuntimeException('Operação falhou!');
    }

    /**
     * Este método não deve ser chamado.
     * @throws RuntimeException Caso o método seja executado
     */
    public function update(string $column = null, string $value = null): bool
    {
        throw new RuntimeException('Não há suporte para atualizações na tabela report');
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function delete(): bool
    {
        // Deleta candidatura do banco
        $query = $this->getConnection()->prepare('DELETE FROM report WHERE id = ?');

        $query->bindValue(1, $this->report->getID());

        return $query->execute();
    }
}