<?php

declare(strict_types=1);

namespace App\Model;

use App\DAO\SelectionDB;
use App\Model\Enumerate\ArtType;
use App\Util\Exception\InvalidAttributeLengthException;
use App\Util\Exception\InvalidAttributeRegexException;
use DateTime;

/**
 * Classe modelo de seleção
 * @package Model
 * @author Ariel Santos <MrXacx>
 */
class Selection extends \App\Model\Template\Entity
{
    /**
     * ID do criador da seleção
     * @var string
     */
    public string $owner;

    /**
     * Valor da seleção
     * @var string
     */
    private float|string $price;

    /**
     * Tipo de arte
     * @var ArtType
     */
    private ArtType $art;

    /**
     * Datas de início e fim
     * @var array<DateTime>
     */
    private array $date;

    /**
     * Datas de início e fim
     * @var array<DateTime>
     */
    private array $time;

    /**
     * Status da seleção
     * @var bool
     */
    private bool $locked = false;
    private string $title;


    /**
     * Obtém um modelo de seleção inicializado
     * 
     * @param array $attr Array associativo contento todas as informações do modelo
     * @return self Instância da classe
     */
    public static function getInstanceOf(array $attr): self
    {

        $entity = new Selection;
        $entity->id = $attr['id'];
        $entity->title = $attr['title'];
        $entity->owner = $attr[SelectionDB::OWNER];
        $entity->price = $attr[SelectionDB::PRICE];
        $entity->art = ArtType::tryFrom($attr[SelectionDB::ART]);

        $iDateTime = DateTime::createFromFormat(SelectionDB::DB_TIMESTAMP_FORMAT, $attr[SelectionDB::START_TIMESTAMP]);
        $fDateTime = DateTime::createFromFormat(SelectionDB::DB_TIMESTAMP_FORMAT, $attr[SelectionDB::END_TIMESTAMP]);

        $entity->date = [
            'start' => $iDateTime,
            'end' => $fDateTime
        ];

        $entity->time = $entity->date;

        $entity->locked = boolval($attr[SelectionDB::LOCKED]);

        return $entity;
    }

    /**
     * @param string $owner ID do criador da seleção
     */
    function setOwner(string $owner)
    {
        $this->owner = $this->validator->isUUID($owner) ? $owner : InvalidAttributeRegexException::throw('owner', __FILE__);
    }

    /**
     * Obtém ID do criador da selção
     *  
     * @return string ID
     */
    public function getOwner(): string
    {
        return $this->owner;
    }

    /**
     * Define Valor da seleção
     * 
     * @param float $price Valor da seleção
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
    }

    /**
     * Obtém Valor da seleção
     * 
     * @return string Preço
     */
    public function getPrice(): float
    {
        return floatval($this->price);
    }

    /**
     * Define datas de início e fim da seleção
     * 
     * @param DateTime $start Data de início
     * @param DateTime $start Data de fim
     */
    public function setDate(DateTime $start, DateTime $end)
    {
        $this->date = ['start' => $start, 'end' => $end];
    }

    /**
     * Define Horários de início e fim da seleção
     * 
     * @param DateTime $start Horário de início
     * @param DateTime $start Horário de fim
     */
    public function setTime(DateTime $start, DateTime $end)
    {
        $this->time = ['start' => $start, 'end' => $end];
    }

    /**
     * Obtém Datas e horários de início e fim do modelo
     * 
     * @return array<DateTime> Vetor de datetimes
     */
    public function getDatetime(): array
    {
        $datetime = [];
        foreach ($this->date as $key => $date) {
            $datetime[$key] = DateTime::createFromFormat(
                SelectionDB::USUAL_TIMESTAMP_FORMAT,
                $date->format(SelectionDB::USUAL_DATE_FORMAT) . " " . $this->time[$key]->format(SelectionDB::DB_TIME_FORMAT)
            );
        }

        return $datetime;
    }

    /**
     * Define tipo de arte da seleção
     * 
     * @param ArtType $art tipo de arte
     */
    public function setArt(ArtType $art)
    {
        $this->art = $art;
    }

    /**
     * Obtém tipo de arte
     * 
     * @return ArtType Arte a ser praticada
     */
    public function getArt(): ArtType
    {
        return $this->art;
    }

    /**
     * Define se a seleção foi endizada
     * @param bool
     */
    public function setLocked(bool $locked): void
    {
        $this->locked = $locked;
    }

    /**
     * Checa se a seleção foi endizada
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function setTitle(string $title): void
    {
        $this->title = $this->validator->isFit($title, SelectionDB::TITLE) ? $title : InvalidAttributeLengthException::throw('title', __FILE__);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function toArray(): array
    {
        return array_filter(array_merge(parent::toArray(), [
            'title' => $this->title,
            'owner' => $this->owner,
            'price' => $this->price ?? null,
            'art' => $this->art ?? null,
            'date' => array_map(fn(DateTime $date) => $date->format(SelectionDB::USUAL_DATE_FORMAT), $this->date ?? []),
            'time' => array_map(fn(DateTime $time) => $time->format(SelectionDB::USUAL_TIME_FORMAT), $this->time ?? []),
            'locked' => boolval($this->locked ?? null),
        ]), fn($value) => isset($value));
    }
}