<?php

namespace App\Model\Tool;

use App\DAO\EnterpriseDB;
use App\Util\Exception\InvalidAttributeLengthException;
use App\Util\Exception\InvalidAttributeRegexException;

trait Location
{

    protected string $CEP;
    protected string $address;
    protected string $neighborhood;
    protected string $city;
    protected string $state;

    /**
     * @param string $CEP
     */
    public function setCEP(string $CEP): void
    {
        $this->CEP = $this->validator->isCEP($CEP) ? $CEP : InvalidAttributeRegexException::throw('CEP', __FILE__);
    }

    /** 
     * @return string CEP
     */
    public function getCEP(): string
    {
        return $this->CEP;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $this->validator->isFit($address, EnterpriseDB::ADDRESS) ? $address : InvalidAttributeLengthException::throw('address', __FILE__);
    }

    /** 
     * @return string address
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $neighborhood
     */
    public function setNeighborhood(string $neighborhood): void
    {
        $this->neighborhood = $this->validator->isFit($neighborhood, EnterpriseDB::NEIGHBORHOOD) ? $neighborhood : InvalidAttributeLengthException::throw('neighborhood', __FILE__);
    }

    /** 
     * @return string neighborhood
     */
    public function getNeighborhood(): string
    {
        return $this->neighborhood;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $this->validator->isFit($city, EnterpriseDB::CITY) ? $city : InvalidAttributeLengthException::throw('city', __FILE__);
    }

    /** 
     * @return string city
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $this->validator->isFit($state, EnterpriseDB::STATE) ? $state : InvalidAttributeLengthException::throw('state', __FILE__);
    }

    /** 
     * @return string state
     */
    public function getState(): string
    {
        return $this->state;
    }

    private function toLocationArray(): array
    {
        return array_filter([
            'CEP' => $this->CEP,
            'address' => $this->address ?? null,
            'neighborhood' => $this->neighborhood ?? null,
            'city' => $this->city,
            'state' => $this->state,
        ], fn($value) => isset($value));
    }

}

?>