<?php

namespace App;

use App\Util\Log;
use Symfony\Component\HttpFoundation\Request;


/**
 * Classe com dados do servidor
 * @package App
 * @author Ariel Santos MrXacx (Ariel Santos)
 */
class Server
{
    /**
     * Linha padrão de início de consulta
     * @var int
     */
    const DEFAULT_OFFSET = 0;

    /**
     * Quantidade padrão de linhas máximas de retornos de consultas
     * @var int
     */
    const DEFAULT_LIMIT = 10;

    /**
     * Quantidade máxima de retornos de consultas
     * @var int
     */
    const MAX_LIMIT = 500;

    public static Log $logger;

    public static function getClientIP(): string{
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Obtém método HTTP
     */
    public static function getHTTPMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public static function replaceHTTPRequestForURI(): void
    {
        $uri = static::getStrippedURI();
        
        if(static::getHTTPMethod() == 'POST'){
            
            if(str_ends_with($uri, '/delete')){
                
                $_SERVER['REQUEST_METHOD'] = 'DELETE';

            } else if(str_ends_with($uri, '/update')){
                
                $_SERVER['REQUEST_METHOD'] = 'PUT';
  
            } 
            
            // Redireciona requisição finalizadas em "/delete" e "/update" para a camada anterior 
            $_SERVER['REQUEST_URI'] = preg_replace('#/((delete)|(update)){1}$#', '', static::getURI());
        }
        
    }

    /**
     * Obtém URI da consulta
     * @return string
     */
    public static function getURI(): string
    {
        return str_replace('src/index.php/', '', $_SERVER['REQUEST_URI']);
    }
    
    

    /**
     * Obtém URI sem parâmetros
     * @return string
     */
    public static function getStrippedURI(): string
    {
        $uri = static::getURI();
        if(false !== $pos = strpos($uri, '?')){
            $uri = substr($uri, 0, $pos);
        }
        return rawurldecode($uri);
    }

    public static function getParameters(): \Symfony\Component\HttpFoundation\ParameterBag {
        
        $requestParameters = static::getHTTPMethod() !== 'GET' ? (array) json_decode(file_get_contents("php://input")) : $_REQUEST;
        
        return (new Request([], [], $requestParameters))->attributes;
    }

}


