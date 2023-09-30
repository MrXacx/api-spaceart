<?php

declare(strict_types=1);

namespace App\Model;

use App\DAO\EnterpriseDB;
use App\Model\Enumerate\AccountType;
use App\Util\Exception\InvalidAttributeLengthException;
use App\Util\Exception\InvalidAttributeRegexException;

/**
 * Classe modelo de empreendimento
 * @package Model
 * @abstract User
 * @author Ariel Santos (MrXacx)
 */
class Enterprise extends \App\Model\Template\User
{
    /**
     * Código nacional de pessoa jurídica
     * @var string
     */
    private string $CNPJ;

    private string $companyName;
    private string $section;

    public function __construct()
    {
        parent::__construct();
        $this->type = AccountType::ENTERPRISE; // Informa à classe mãe o tipo de conta que ela está formando
    }

    /**
     * Obtém um modelo de usuário inicializado
     * 
     * @param array $attr Array associativo contento todas as informações do modelo
     * @return self Instância da classe
     */
    public static function getInstanceOf(array $attr): self
    {
        $entity = new Enterprise;

        foreach ($attr as $key => $value) { // Itera array informado em busca de atributos úteis à classe
            $atributeName = match ($key) { // Retorna o nome do atributo correspondente na classe
                'id' => 'id',
                'index', 'placing' => 'index',
                EnterpriseDB::EMAIL => 'email',
                EnterpriseDB::PASSWORD => 'password',
                EnterpriseDB::NAME => 'name',
                EnterpriseDB::CNPJ => 'CNPJ',
                EnterpriseDB::CEP => 'CEP',
                EnterpriseDB::STATE => 'state',
                EnterpriseDB::CITY => 'city',
                EnterpriseDB::NEIGHBORHOOD => 'neighborhood',
                EnterpriseDB::ADDRESS => 'address',
                EnterpriseDB::PHONE => 'phone',
                EnterpriseDB::SITE => 'website',
                EnterpriseDB::RATE => 'rate',
                EnterpriseDB::COMPANY_NAME => 'companyName',
                EnterpriseDB::SECTION => 'section',
                EnterpriseDB::DESCRIPTION => 'description',
                EnterpriseDB::VERIFIED => 'verified',
                default => null
            };

            if (isset($atributeName)) { // executa apenas caso um correspondente tenha sido encontrado
                $entity->$atributeName = $value;
            }
        }

        return $entity;
    }

    public function setCNPJ(string $CNPJ): void
    {
        $this->CNPJ = $this->validator->isCNPJ($CNPJ) ? $CNPJ : InvalidAttributeRegexException::throw('CNPJ', __FILE__);
    }

    public function getCNPJ(): string
    {
        return $this->CNPJ;
    }

    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $this->validator->isFit($companyName, EnterpriseDB::COMPANY_NAME) ? $companyName : InvalidAttributeLengthException::throw('company_name', __FILE__);
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }
    public function setSection(string $section): void
    {
        $this->section = $this->validator->isFit($section, EnterpriseDB::SECTION) ? $section : InvalidAttributeLengthException::throw('section', __FILE__);
    }

    public function getSection(): string
    {
        return $this->section;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'CNPJ' => $this->CNPJ ?? null,
                'companyName' => $this->companyName,
                'section' => $this->section,
            ]
        );
    }
}
