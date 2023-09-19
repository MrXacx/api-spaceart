<?php

namespace App\Util;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger;

class Log{
    private const path = __DIR__. './../../../tmp/log/';
    /**
     * Summary of currentFile
     * @var string
     */
    private string $currentFile = '';
    /**
     * Summary of __construct
     * @param Logger $logger
     * @param Level $level
     */
    public function __construct(private Logger $logger, private Level $level) {}
    /**
     * Summary of build
     * @return void
     */
    public function build(): void{
        $file =  self::path . 'SpacearAPI.log';
        if($this->currentFile != $file){
            $this->logger->pushHandler(new RotatingFileHandler($file, 366, $this->level));
        }
    }

    /**
     * Summary of push
     * @param string $message
     * @param Level $level
     * @return void
     */
    public function push(string $message, Level $level): void{
        $levelMethod = match($level){
            Level::Critical => 'critical',
            Level::Emergency => 'emergency',
            Level::Error => 'error',
            Level::Alert => 'alert',
            Level::Warning => 'warning',
            Level::Notice => 'notice',
            Level::Info => 'info',
            Level::Debug => 'debug'
        }; 
        
        $this->logger->$levelMethod($message);
    }
}

?>