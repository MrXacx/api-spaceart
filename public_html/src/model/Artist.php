<?php

declare(strict_types=1);

namespace App\Model;

use App\DAO\ArtistDB;
use App\Model\Enumerate\AccountType;
use App\Model\Enumerate\ArtType;
use App\Util\Exception\InvalidAttributeRegexException;
use DateTime;

/**
 * Classe modelo de artista
 * @package Model
 * @abstract User
 * @author Ariel Santos (MrXacx)
 */
class Artist extends \App\Model\Template\User
{
    /**
     * Código de Pessoa Física
     * @var string
     */
    private string $CPF;

    /**
     * Tipo de art
     * @var ArtType|string
     */
    private ArtType|string $art;

    private DateTime $birthday;

    /**
     * Pretensão salarial por hora
     * @var string|float
     */
    private string|float $wage;

    public function __construct() {
        $this->type = AccountType::ARTIST; // Informa à classe mãe o tipo de conta que ela está formando
    }

    public static function getInstanceOf(array $attr): self
    {
        $entity = new Artist;

        foreach ($attr as $key => $value) {

            $atributeName = match ($key) {
                'id' => 'id',
                'index' => 'index',
                ArtistDB::EMAIL => 'email',
                ArtistDB::PASSWORD => 'password',
                ArtistDB::NAME => 'name',
                ArtistDB::PHONE => 'phone',
                ArtistDB::CEP => 'CEP',
                ArtistDB::STATE => 'state',
                ArtistDB::CITY => 'city',
                ArtistDB::CPF => 'CPF',
                ArtistDB::ART => 'art',
                ArtistDB::WAGE => 'wage',
                ArtistDB::SITE => 'website',
                ArtistDB::RATE => 'rate',
                ArtistDB::BIRTHDAY => 'birthday',
                ArtistDB::DESCRIPTION => 'description',
                ArtistDB::VERIFIED => 'verified',

                default => null
            };

            if (isset($atributeName)) {
                $entity->$atributeName = $value;
            }

        }

        return $entity;
    }

    /**
     * Insere código de pessoa física
     * @param string $CPF código
     */
    public function setCPF(string $CPF): void
    {
        $this->CPF = $this->validator->isCPF($CPF) ? $CPF : InvalidAttributeRegexException::throw('CPF', __FILE__);
    }

    /**
     * Obtém código de pessoa física
     * @return string Número de identificação
     */
    public function getCPF(): string
    {
        return $this->CPF;
    }

    /**
     * Insere tipo de arte
     * @param ArtType
     */
    public function setArt(ArtType $art): void
    {
        $this->art = $art;
    }

    /**
     * Obtém tipo de arte
     * @return ArtType 
     */
    public function getArt(): ArtType
    {
        return $this->art;
    }
    /**
     * Insere pretensão salarial
     * @param float $wage
     */
    public function setWage(float $wage): void
    {
        $this->wage = $wage;
    }

    /**
     * Obtém pretensão salarial
     * @return string Número de identificação
     */
    public function getWage(): float
    {
        return $this->wage;
    }
    
    
    public function setBirthday(DateTime $birthday): void
    {
        $this->birthday = $birthday;
    }


    public function getBirthday(): DateTime
    {
        return $this->birthday;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'CPF' => $this->CPF ?? null,
            'birthday' => $this->birthday->format(ArtistDB::USUAL_DATE_FORMAT),
            'art' => $this->art,
            'wage' => $this->wage
        ]);
    }
}