<?php

namespace App\Model;

use App\DAO\Template\DatabaseAcess;
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
        $model->message = $attr['message'];
        $model->media = $attr['media'];
        $model->postTime = DateTime::createFromFormat(DatabaseAcess::DB_TIMESTAMP_FORMAT, $attr['post_time']);

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
            $this->message,
            $this->media,
            $this->postTime->format(DatabaseAcess::USUAL_TIMESTAMP_FORMAT)
        ]);
    }
}


?>

