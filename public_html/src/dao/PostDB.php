<?php

declare(strict_types=1);

namespace App\DAO;

use App\DAO\Template\DatabaseAcess;
use App\Model\Post;
use RuntimeException;


class PostDB extends DatabaseAcess
{
    public const MESSAGE = 'message';
    public const POST_TIME = 'post_time';
    public const MEDIA = 'media';
    public const AUTHOR = 'author';



    function __construct(private Post $post)
    {
        $this->post = $post;
        parent::__construct();
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function create(): bool
    {
        $this->post->setID($this->getRandomID()); // Gera uuid

        // Passa query SQL de criação
        $query = $this->getConnection()->prepare('INSERT INTO post (id, author, message, media) VALUES (UUID(),?,?,?)');

        // Substitui interrogações pelos valores dos atributos
        $query->bindValue(1, $this->post->getAuthor());
        $query->bindValue(2, $this->post->getMessage());
        $query->bindValue(3, $this->post->getMedia());

        return $query->execute();
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function getList(int $offset = 0, int $limit = 10): array
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare("SELECT * FROM post WHERE author = ? LIMIT $limit OFFSET $offset");
        $query->bindValue(1, $this->post->getAuthor()); // Substitui interrogação na query pelo ID passado

        if ($query->execute()) { // Executa se consulta não falhar
            return array_map(fn($post) => Post::getInstanceOf($post), $this->fetchRecord($query));
        }

        // Executa em caso de falhas esperadas
        throw new RuntimeException('Operação falhou!');
    }
    /**
     * Obtém lista de posts aleatórios
     */
    public function getRandomList(int $limit = 10): array
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare("SELECT * FROM post ORDER BY RAND() LIMIT $limit");

        if ($query->execute()) { // Executa se consulta não falhar
            return array_map(fn($post) => Post::getInstanceOf($post), $this->fetchRecord($query));
        }

        // Executa em caso de falhas esperadas
        throw new RuntimeException('Operação falhou!');
    }

    public function getPost(): Post
    {
        // Determina query SQL de leitura
        $query = $this->getConnection()->prepare("SELECT * FROM post WHERE id = ?");
        $query->bindValue(1, $this->post->getID()); // Substitui interrogação na query pelo ID passado

        if ($query->execute()) { // Executa se consulta não falhar
            return Post::getInstanceOf($this->fetchRecord($query, false));
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
        throw new RuntimeException('Não há suporte para atualizações na tabela Post');
    }

    /**
     * @see abstracts/DatabaseAcess.php
     */
    public function delete(): bool
    {
        // Deleta candidatura do banco
        $query = $this->getConnection()->prepare('DELETE FROM post WHERE id = ?');

        $query->bindValue(1, $this->post->getID());

        return $query->execute();
    }
}