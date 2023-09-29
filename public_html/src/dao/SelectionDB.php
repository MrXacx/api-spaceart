<?php

declare(strict_types=1);

namespace App\DAO;

use App\DAO\Template\DatabaseAcess;
use App\Model\Selection;
use RuntimeException;

/**
 * Classe de maniupulação da tabela Selections
 * 
 * @package DAO
 * @author Ariel Santos (MrXacx)
 */
class SelectionDB extends DatabaseAcess
{

    public const OWNER = 'owner';
    public const ART = 'art';
    public const START_TIMESTAMP = 'start_timestamp';
    public const END_TIMESTAMP = 'end_timestamp';
    public const PRICE = 'price';
    public const LOCKED = 'locked';
    public const TITLE = 'title';

    /**
     * Modelo de seleção a ser manipulado
     * @var Selection
     */
    private Selection $selection;

    /**
     * @param Selection $selection Modelo de seleção a ser manipulado
     */
    function __construct(Selection $selection)
    {
        $this->selection = $selection;
        parent::__construct();
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function create(): bool
    {
        $datetime = $this->selection->getDatetime(); // Obtém datas e horários de início e fim

        // Passa query SQL de criação
        $query = $this->getConnection()->prepare('INSERT INTO selection (id, owner, price, start_timestamp, end_timestamp, art, title) VALUES (UUID(),?,?,?,?,?,?)');

        // Substitui interrogações pelos valores dos atributos
        $query->bindValue(1, $this->selection->getOwner());
        $query->bindValue(2, $this->selection->getPrice());
        $query->bindValue(3, $datetime['start']->format(parent::DB_TIMESTAMP_FORMAT));
        $query->bindValue(4, $datetime['end']->format(parent::DB_TIMESTAMP_FORMAT));
        $query->bindValue(5, $this->selection->getArt()->value);
        $query->bindValue(6, $this->selection->getTitle());

        return $query->execute();
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function getList(int $offset = 0, int $limit = 10): array
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare("SELECT * FROM selection AS sel WHERE art = ? ORDER BY sel.start_timestamp LIMIT $limit OFFSET $offset");

        $query->bindValue(1, $this->selection->getArt()->value);

        if ($query->execute()) { // Executa se consulta não falhar
            return array_map(fn($agreement) => Selection::getInstanceOf($agreement), $this->fetchRecord($query));
        }

        throw new RuntimeException('Operação falhou!'); // Executa em caso de falhas esperadas
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function getListOfOwner(int $offset = 0, int $limit = 10): array
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare("SELECT * FROM selection AS sel WHERE owner = ? ORDER BY sel.start_timestamp LIMIT $limit OFFSET $offset");

        $query->bindValue(1, $this->selection->getOwner()); // Substitui interrogação na query pelo ID passado

        if ($query->execute()) { // Executa se consulta não falhar
            return array_map(fn($agreement) => Selection::getInstanceOf($agreement), $this->fetchRecord($query));
        }

        throw new RuntimeException('Operação falhou!'); // Executa em caso de falhas esperadas
    }

    /**
     * Obtém modelo de seleção configurado com base nos dados do banco
     * 
     * @return Selection Modelo da seleção
     * @throws RuntimeException Falha causada pela conexão com o banco de dados
     */
    public function getSelection(): Selection
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare('SELECT * FROM selection WHERE id = ?');
        $query->bindValue(1, $this->selection->getID()); // Substitui interrogação na query pelo ID passado

        if ($query->execute()) { // Executa se a query for aceita
            return Selection::getInstanceOf($this->fetchRecord($query, false));
        }

        // Executa em caso de falhas esperadas
        throw new RuntimeException('Operação falhou!');
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function update(string $column, string $value): bool
    {
        // Passa query SQL de atualização
        $query = $this->getConnection()->prepare("UPDATE selection SET $column = ? WHERE id = ?");

        // Substitui interrogações pelos valores das variáveis
        $query->bindValue(1, $value);
        $query->bindValue(2, $this->selection->getID());

        return $query->execute();
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function delete(): bool
    {
        // Deleta seleção do banco
        $query = $this->getConnection()->prepare('DELETE FROM selection WHERE id = ?');
        $query->bindValue(1, $this->selection->getID());
        return $query->execute();
    }
}