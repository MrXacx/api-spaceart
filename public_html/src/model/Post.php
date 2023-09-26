<?php

namespace App\Model;

use DateTime;

/**
 * Classe modelo de postagem
 * @package Model
 * @author Ariel Santos <MrXacx>
 */
class Post extends \App\Model\Template\Entity{
    private string $author;
    private string $message;
    private string $video_url;
    private string $image;
    private DateTime $postTime;
    
    public function setAuthor(string $author): void{
        $this->author = $author;
    }
    
    public function getAuthor(): string{
        return $this->author;
    }
    
    public function setMessage(string $message): void{
        $this->$message = $message;
    }
    
    public function getMessage(): string{
        return $this->message;
    }
    
    public function setImage(string $image): void{
        $this->image = $image;
    }
    
    public function getImage(): string{
        return $this->image;
    }
    
    public function setVideoURL(string $videoURL): void{
       $this->videoURL = $videoURL;
    }
    
    public function getVideoURL(): string{
        return $this->videoURL;
    }
    
    public function setPostTime(DateTime $postTime): void{
       $this->postTime = $postTime;
    }
    
    public function getPostTime(): DateTime{
        return $this->postTime;
    }

}


?>
