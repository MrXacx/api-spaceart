<?php

declare(strict_types=1);

namespace App\DAO;

use App\DAO\UsersDB;
use App\Model\Artist;
use RuntimeException;

/**
 * Classe de maniupulação da tabela Users
 * @package DAO
 * @author Ariel Santos <MrXacx>
 */
class ArtistDB extends UsersDB
{
    public const ART = 'art';
    public const WAGE = 'wage';
    public const CPF = 'cpf';
    public const BIRTHDAY = 'birthday';

    private Artist $artist;

    /**
     * @param Artist $artist Modelo de empreendimento a ser manipulado
     */
    function __construct(Artist $artist = null)
    {
        parent::__construct($artist);
        if($artist) $this->artist = $artist;
    }

    public static function isEditalbeColumn(string $column): bool
    {
        return is_int(
            array_search($column, [
                self::ART,
                self::WAGE,
            ])
        );
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function create(): bool
    {
        if (parent::create()) { // Executa se o usuário foi criado

            // Passa query SQL de criação
            $query = $this->getConnection()->prepare('INSERT INTO artist (id, CPF, art, wage, birthday) VALUES (?,?,?,?,?)');

            // Substitui interrogações pelos valores dos atributos
            $query->bindValue(1, $this->artist->getID());
            $query->bindValue(2, $this->artist->getCPF());
            $query->bindValue(3, $this->artist->getArt()->value);
            $query->bindValue(4, $this->artist->getWage());
            $query->bindValue(5, $this->artist->getBirthday()->format(parent::DB_DATE_FORMAT));


            if ($query->execute()) { // Executa a inserção funcionar
                return true;
            }

            // Essa linha é essencial para não exista um registro em users que não possa ser encontrado em artist ou enterprise
            $this->delete();

        }
        return false;
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function getList(int $offset = 0, int $limit = 10): array
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare(
            "SELECT * FROM artist_view LIMIT $limit OFFSET $offset"
        );

        if ($query->execute()) { // Executa se consulta não falhar
            return array_map(fn($user) => Artist::getInstanceOf($user), $this->fetchRecord($query));
        }
        throw new RuntimeException('Operação falhou!'); // Executa se alguma falha esperdada ocorrer
    }

    /**
     * Consulta lista aleatória de artistas na tabela filtrada pela localização
     * 
     * @param int $offset Linha inicial da consulta
     * @param int $limit Número máximo de registros retornados
     */
    public function getRandomListByLocation(int $offset = 0, int $limit = 10): array
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare(
            "SELECT * FROM artist_view
            WHERE city = ? AND state = ?
            ORDER BY RAND()
            LIMIT $limit OFFSET $offset"
        );

        $query->bindValue(1, $this->artist->getCity());
        $query->bindValue(2, $this->artist->getState());

        if ($query->execute()) { // Executa se consulta não falhar
            return array_map(fn($user) => Artist::getInstanceOf($user), $this->fetchRecord($query));
        }
        throw new RuntimeException('Operação falhou!'); // Executa se alguma falha esperdada ocorrer
    }

    /**
     * Consulta lista aleatória de artistas na tabela filtrada pelo tipo de arte
     * 
     * @param int $offset Linha inicial da consulta
     * @param int $limit Número máximo de registros retornados
     */
    public function getRandomListByArt(int $offset = 0, int $limit = 10): array
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare(
            "SELECT * FROM artist_view
            WHERE art = ?
            ORDER BY RAND()
            LIMIT $limit OFFSET $offset"
        );

        $query->bindValue(1, $this->artist->getArt()->value);

        if ($query->execute()) { // Executa se consulta não falhar
            return array_map(fn($user) => Artist::getInstanceOf($user), $this->fetchRecord($query));
        }
        throw new RuntimeException('Operação falhou!'); // Executa se alguma falha esperdada ocorrer
    }

    /**
     * Consulta lista de artistas na tabela filtrada pelo nome
     * 
     * @param int $offset Linha inicial da consulta
     * @param int $limit Número máximo de registros retornados
     */

    public function getListByName(int $offset = 0, int $limit = 10): array
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare(
            "SELECT * FROM artist_view
            WHERE name LIKE ?
            ORDER BY name
            LIMIT $limit OFFSET $offset"
        );

        $query->bindValue(1, $this->artist->getName() . '%');

        if ($query->execute()) { // Executa se consulta não falhar
            return array_map(fn($user) => Artist::getInstanceOf($user), $this->fetchRecord($query));
        }
        throw new RuntimeException('Operação falhou!'); // Executa se alguma falha esperdada ocorrer
    }

    /**
     * Obtém modelo de artista com dados não sensíveis com base no id
     * @return Artist Modelo de artista
     */
    public function getPublicDataFromUserForID(): Artist
    {
        // Define query SQL para obter todas as colunas da linha do usuário
        $query = $this->getConnection()->prepare('SELECT * FROM artist_view WHERE id = ?');
        $query->bindValue(1, $this->artist->getID()); // Substitui interrogação pelo ID

        if ($query->execute()) { // Executa se a query for aceita
            return Artist::getInstanceOf($this->fetchRecord($query, false));
        }
        // Executa em caso de falhas esperadas
        throw new RuntimeException('Operação falhou!');
    }
    /**
     * Obtém modelo de artista com dados não sensíveis com base no Index
     * @return Artist Modelo de artista
     */
    public function getPublicDataFromUserForIndex(): Artist
    {
        // Define query SQL para obter todas as colunas da linha do usuário
        $query = $this->getConnection()->prepare('SELECT * FROM artist_view WHERE artist_view.index = ?');
        $query->bindValue(1, $this->artist->getIndex()); // Substitui interrogação pelo Index

        if ($query->execute()) { // Executa se a query for aceita
            return Artist::getInstanceOf($this->fetchRecord($query, false));
        }
        // Executa em caso de falhas esperadas
        throw new RuntimeException('Operação falhou!');
    }

    /**
     * Obtém modelo de artista com todos os dados disponíveis
     * @return Artist Modelo de artista
     */
    public function getUser(): Artist
    {

        // Define query SQL para obter todas as colunas da linha do usuário
        $query = $this->getConnection()->prepare('SELECT * FROM artist INNER JOIN users ON artist.id = users.id WHERE token = ?');
        $query->bindValue(1, $this->artist->getID()); // Substitui interrogação pelo ID

        if ($query->execute()) { // Executa se a query for aceita
            return Artist::getInstanceOf($this->fetchRecord($query, false));
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
        $query = $this->getConnection()->prepare("UPDATE artist SET $column = ? WHERE id = ALL (SELECT id FROM users WHERE token = ?)");

        // Substitui interrogações
        $query->bindValue(1, $value);
        $query->bindValue(2, $this->artist->getID());

        return $query->execute();
    }

    public function getStats(): array{
        $query = $this->getConnection()
        ->prepare(
            "
                SELECT art, COUNT(*) as total 
                FROM artist
                GROUP BY art;
            "
        );

        if($query->execute()){
            return $this->fetchRecord($query, false);
        }

        throw new \RuntimeException('Operação falhou!');   
    }
}
