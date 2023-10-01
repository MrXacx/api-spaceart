<?php

namespace App\Model;

use App\DAO\PostDB;
use DateTime;

/**
 * Classe modelo de postagem
 * @package Model
 * @author Ariel Santos <MrXacx>
 */
class Post extends \App\Model\Template\Entity{
    private string $author;
    private string $message;
    private string $media;
    private DateTime $postTime;
    
    public static function getInstanceOf(array $attr): self {
        $model = new Post;
        $model->id = $attr['id'];
        
        $model->author = $attr[PostDB::AUTHOR];
        $model->message = $attr[PostDB::POST_TIME];
        $model->media = $attr[PostDB::POST_TIME];
        $model->postTime = DateTime::createFromFormat(PostDB::DB_TIMESTAMP_FORMAT, $attr[PostDB::POST_TIME]);

        return $model;
    }

    public function setAuthor(string $author): void{
        $this->author = $author;
    }
    
    public function getAuthor(): string{
        return $this->author;
    }
    
    public function setMessage(string $message): void{
        $this->message = $message;
    }
    
    public function getMessage(): string{
        return $this->message;
    }
    
    public function setMedia(string $media): void{
        $this->media = $media;
    }
    
    public function getMedia(): string{
        return $this->media;
    }
    
    
    public function setPostTime(DateTime $postTime): void{
       $this->postTime = $postTime;
    }
    
    public function getPostTime(): DateTime{
        return $this->postTime;
    }

    public function toArray(): array{
        return array_merge(parent::toArray(), [
            PostDB::AUTHOR => $this->author,
            PostDB::MESSAGE => $this->message,
            PostDB::MEDIA => $this->media,
            PostDB::POST_TIME => $this->postTime->format(PostDB::USUAL_TIMESTAMP_FORMAT)
        ]);
    }
}


?>
