<?php

declare(strict_types=1);

namespace App\DAO;

use App\DAO\Template\DatabaseAcess;
use App\Model\Application;
use App\Model\Selection;
use RuntimeException;

/**
 * Classe de maniupulação da tabela Selection_Applications
 * 
 * @package DAO
 * @author Ariel Santos (MrXacx)
 */
class ApplicationDB extends DatabaseAcess
{

    public const ARTIST = 'artist';
    public const SELECTION = 'selection';
    public const LAST_CHANGE = 'last_change';

    /**
     * Modelo de candidatura a ser manipulado
     * @var Application
     */
    private Application $application;


    /**
     * @param Application $application Modelo de candidatura a ser manipulado
     * @param Selection $selection Modelo de seleção a ser considerado na manipulação [opcional]
     */
    function __construct(Application $application)
    {
        $this->application = $application;
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

        // Passa query SQL de criação
        $query = $this->getConnection()->prepare('INSERT INTO selection_application (selection, artist) VALUES (?,?)');

        // Substitui interrogações pelos valores dos atributos
        $query->bindValue(1, $this->application->getSelection());
        $query->bindValue(2, $this->application->getUser());

        return $query->execute();
    }

    /**
     * Obtém todos os dados de uma aplicação em específico
     * @return Application objeto da aplicação
     */
    public function getApplication(): Application
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare('SELECT * FROM selection_application WHERE selection = ? AND artist = ?');
        
        $query->bindValue(1, $this->application->getSelection()); // Substitui interrogação na query pelo ID da seleção
        $query->bindValue(2, $this->application->getUser()); // Substitui interrogação na query pelo ID do usuário

        if ($query->execute()) { // Executa se a query for aceita
            return Application::getInstanceOf($this->fetchRecord($query, false));
        }

        // Executa em caso de falhas esperadas
        throw new RuntimeException('Operação falhou!');
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function getList(int $offset = 0, int $limit = 10): array
    {
        // Determina query SQL de leitura

        $query = $this->getConnection()->prepare("SELECT * FROM selection_application WHERE selection = ? LIMIT $limit OFFSET $offset");
        $query->bindValue(1, $this->application->getSelection()); // Substitui interrogação na query pelo ID passado

        if ($query->execute()) { // Executa se consulta não falhar
            return array_map(fn($application) => Application::getInstanceOf($application), $this->fetchRecord($query));
        }

        // Executa em caso de falhas esperadas
        throw new RuntimeException('Operação falhou!');
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function update(string $column = null, string $value = null): bool
    {
        throw new RuntimeException('Não há suporte para atualizações na tabela selection_application');
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function delete(): bool
    {
        // Deleta candidatura do banco
        $query = $this->getConnection()->prepare('DELETE FROM selection_application WHERE selection = ? AND artist = ?');

        $query->bindValue(1, $this->application->getSelection());
        $query->bindValue(2, $this->application->getUser());

        return $query->execute();
    }
}