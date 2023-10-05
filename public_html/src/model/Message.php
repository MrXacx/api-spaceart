<?php

namespace App\Model;

use App\DAO\MessageDB;
use App\Util\Exception\InvalidAttributeLengthException;
use App\Util\Exception\InvalidAttributeRegexException;
use DateTime;

/**
 * Classe de modelo de mensagens
 * @package Model
 * @author Ariel Santos <MrXacx>
 */
class Message extends \App\Model\Template\Entity
{
    /**
     * ID do emissor
     * @var string
     */
    private string $sender;

    /**
     * ID do canal
     * @var string
     */
    private string $chat;

    /**
     * Conteúdo da mensagem
     * @var string
     */
    private string $content;

    /**
     * Momento do envio da mensagem
     * @var DateTime
     */
    private DateTime $timestamp;

    /**
     * @param string ID do chat
     */
    function __construct(string $chat)
    {
        parent::__construct();
        $this->chat = $this->validator->isUUID($chat) ? $chat : InvalidAttributeRegexException::throw('chat', __FILE__);
    }

    public static function getInstanceOf(array $attr): self
    {
        $entity = new Message($attr[MessageDB::CHAT]);

        $entity->sender = $attr[MessageDB::SENDER];
        $entity->content = $attr[MessageDB::CONTENT];
        $entity->timestamp = DateTime::createFromFormat(MessageDB::DB_TIMESTAMP_FORMAT, $attr[MessageDB::DATETIME]);
        return $entity;
    }

    public function toArray(): array
    {
        return [
            'sender' => $this->sender,
            'chat' => $this->chat,
            'content' => $this->content,
            'datetime' => $this->timestamp->format(MessageDB::USUAL_TIMESTAMP_FORMAT),
        ];
    }

    /**
     * Define ID do emissor
     * @param string
     */
    public function setSender(string $sender)
    {
        $this->sender = $this->validator->isUUID($sender) ? $sender : InvalidAttributeRegexException::throw('sender', __FILE__);
    }

    /**
     * Obtém ID do emissor
     * @return string
     */
    public function getSender(): string
    {
        return $this->sender;
    }

    /**
     * Obtém ID do canal
     * @param string
     */
    public function getChat(): string
    {
        return $this->chat;
    }

    /**
     * Define conteúdo da mensagem
     * @param string
     */
    public function setContent(string $content): void
    {
        $this->content = $this->validator->isFit($content) ? $content : InvalidAttributeLengthException::throw('content', __FILE__);
    }

    /**
     * Obtém conteúdo da mensagem
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Define marco temporal do envio da mensagem
     * @param DateTime $timestemp
     */
    public function setTimestamp(DateTime $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * Obtém marco temporal do envio da mensagem
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }
}