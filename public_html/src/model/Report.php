<?php

namespace App\Model;

use App\DAO\ReportDB;
use App\Util\Exception\InvalidAttributeLengthException;
use App\Util\Exception\InvalidAttributeRegexException;

/**
 * Classe modelo de denúncia
 * @package Model
 * @author Ariel Santos <MrXacx>
 */
class Report extends \App\Model\Template\Entity
{
    /**
     * ID do denunciador
     * @var string
     */
    private string $reporter;

    /**
     * ID do denunciado
     * @var string
     */
    private string $reported;

    /**
     * Motivo da denúncia
     * @var string
     */
    private string $reason;

    /**
     * Se a denúncia foi acolhida
     * @var bool
     */
    private bool $accepted;

    function __construct(string $reporter)
    {
        parent::__construct();
        $this->reporter = $this->validator->isUUID($reporter) ? $reporter : InvalidAttributeRegexException::throw('reporter', __FILE__);
    }

    public static function getInstanceOf(array $attr): self
    {
        $entity = new Report($attr[ReportDB::REPORTER]);

        $entity->id = $attr['id'];
        $entity->reported = $attr[ReportDB::REPORTED];
        $entity->reason = $attr[ReportDB::REASON];
        $entity->accepted = boolval($attr[ReportDB::ACCEPTED]);

        return $entity;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'reporter' => $this->reporter,
            'reported' => $this->reported,
            'reason' => $this->reason,
            'accepted' => boolval($this->accepted),
        ]);
    }

    /**
     * Obtém id do denunciador
     * @return string Denunciador
     */
    public function getReporter(): string
    {
        return $this->reporter;
    }

    /**
     * Define id do denunciado
     * @param string Denunciado
     */
    public function setReported(string $reported): void
    {
        $this->reported = $this->validator->isUUID($reported) ? $reported : InvalidAttributeRegexException::throw('reported', __FILE__);
    }

    /**
     * Obtém id do denunciado
     * @return string Denunciado
     */
    public function getReported(): string
    {
        return $this->reported;
    }

    /**
     * Define razão da denúncia
     * @param string Motivo da denúncia
     */
    public function setReason(string $reason): void
    {
        $this->reason = $this->validator->isFit($reason) ? $reason : InvalidAttributeLengthException::throw('reason', __FILE__);
    }

    /**
     * Obtém razão da denúncia
     * @return string Motivo da denúncia
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * Define se a denúncia foi aceita
     * @param bool
     */
    public function setAccepted(bool $accepted): void
    {
        $this->accepted = $accepted;
    }

    /**
     * Retorna se a denuncia foi aceita
     * @param string
     */
    public function isAccepted(): bool
    {
        return $this->accepted;
    }
}