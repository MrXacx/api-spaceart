<?php

declare(strict_types=1);

namespace App\Controller;

use App\DAO\PostDB;
use App\Model\Post;
use App\Util\Cache;
use App\Controller\Tool\Controller;

/**
 * Controlador de Post e mensagens
 * 
 * @package Controller
 * @author Ariel Santos <MrXacx>
 */
final class PostController
{
    use Controller;

    /**
     * Armazena um Post
     * @return bool true caso a operação funcione corretamente
     */
    public function storePost(): bool
    {

        $post = new Post;
        $post->setAuthor($this->parameterList->getString('author'));
        $post->setMessage($this->parameterList->getString('message'));
        $post->setMedia($this->parameterList->getString('media'));

        $db = new PostDB($post);
        return $db->create();

    }

    /**
     * Obtém dados de um Post em específico
     * @return array Todos os dados de um Post em específico
     */
    public function getPost(): array
    {
        $post = new Post;
        $post->setID($this->parameterList->getString('id'));

        $db = new PostDB($post); // Inicia objeto para manipular o Post
        $post = $this->filterNulls($db->getPost()->toArray()); // Obtém dados do post
        Controller::$cache->create($post, Cache::LARGE_INTERVAL_STORAGE); 
        return $post;

    }

    /**
     * Obtém lista de Posts
     * @return array
     */
    public function getPostList(): array
    {

        $offset = $this->fetchListOffset(); // Obtém posição de início da leitura
        $limit = $this->fetchListLimit(); // Obtém máximo de elementos da leitura


        $list = ($this->parameterList->getString('references') == 'author') ?
            $this->getUserPostList($offset, $limit) :
            $this->getRandomPostList($limit);
            
         Controller::$cache->create($list, Cache::LARGE_INTERVAL_STORAGE);
         return $list;
    }

    /**
     * Obtém lista de Posts
     * @return array
     */
    public function getUserPostList(int $offset, int $limit): array
    {

        $offset = $this->fetchListOffset(); // Obtém posição de início da leitura
        $limit = $this->fetchListLimit(); // Obtém máximo de elementos da leitura


        $post = new Post;
        $post->setAuthor($this->parameterList->getString('author'));

        $db = new PostDB($post);
        $list = $db->getList($offset, $limit);
        return array_map(fn($author) => $this->filterNulls($author->toArray()), $list);
    }

    /**
     * Obtém lista de Posts
     * @return array
     */
    public function getRandomPostList(int $limit): array
    {

        $db = new PostDB(new Post);
        $list = $db->getRandomList($limit);
        return array_map(fn($author) => $this->filterNulls($author->toArray()), $list);
    }


    /**
     * Deleta Post
     * @return bool true caso a operação funcione corretamente
     */
    public function deletePost(): bool
    {
        $post = new Post;
        $post->setID($this->parameterList->getString('id'));
       
        $db = new postDB($post);
        return $db->delete();
    }

}

?>
