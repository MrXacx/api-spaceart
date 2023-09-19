<?php

namespace App\Model\Tool;

use App\Util\Exception\InvalidAttributeFormatException;

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
        $this->CEP = $this->validator->isCEP($CEP) ? $CEP : InvalidAttributeFormatException::throw('CEP');
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
        $this->address = $address;
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
        $this->neighborhood = $neighborhood;
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
        $this->city = $city;
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
        $this->state = $state;
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