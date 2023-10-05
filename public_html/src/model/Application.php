<?php

namespace App\Model;

use App\DAO\ApplicationDB;
use App\Util\Exception\InvalidAttributeRegexException;
use DateTime;

/**
 * Classe modelo de aplicação a uma seleção
 * @package Model
 * @author Ariel Santos <MrXacx>
 */
class Application extends \App\Model\Template\Entity
{
    /**
     * ID do usuário
     * @var string
     */
    private string $user;

    /**
     * ID da seleção
     * @var string
     */
    private string $selection;

    /**
     * Data da última alteração na aplicação
     * @var DateTime
     */
    private DateTime $lastChange;


    /**
     * @param string ID da seleção
     */
    function __construct(string $selection)
    {
        parent::__construct();
        $this->selection = $this->validator->isUUID($selection) ? $selection : InvalidAttributeRegexException::throw('selection', __FILE__);
    }

    /**
     * Obtém ID da seleção
     * @return string
     */
    public function getSelection(): string
    {
        return $this->selection;
    }

    /**
     * Insere ID do usuário
     * @param string
     */
    public function setUser(string $user): void
    {
        $this->user = $this->validator->isUUID($user) ? $user : InvalidAttributeRegexException::throw('user', __FILE__);
    }

    /**
     * Obtém ID do usuário
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    public static function getInstanceOf(array $attr): self
    {
        $entity = new Application($attr[ApplicationDB::SELECTION]);
        $entity->user = $attr[ApplicationDB::ARTIST];
        $entity->lastChange = DateTime::createFromFormat(ApplicationDB::DB_TIMESTAMP_FORMAT, $attr[ApplicationDB::LAST_CHANGE]);

        return $entity;
    }

    public function toArray(): array
    {
        return [
            'selection' => $this->selection,
            'user' => $this->user,
            'last_change' => $this->lastChange->format(ApplicationDB::USUAL_TIMESTAMP_FORMAT),
        ];
    }
}