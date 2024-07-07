<?php

namespace App\Repositories;

use App\Exceptions\NotFoundModelException;
use App\Models\Address;
use App\Repositories\Contracts\WebClientRepository;
use App\Services\Logger;
use GuzzleHttp\Exception\GuzzleException;
use Nette\Utils\Json;

class PostalCodeRepository extends WebClientRepository
{
    private string $uri = 'https://brasilapi.com.br/api/cep/v1';

    public static function make(): PostalCodeRepository
    {
        return new PostalCodeRepository;
    }

    /**
     * @throws GuzzleException
     * @throws NotFoundModelException
     */
    public function fetch(string $postalCode): Address
    {
        $logger = new Logger;
        $url = "$this->uri/$postalCode";

        $response = $this->client->get($url); // Request
        $logger->request("Request to external service: $url");

        $status = $response->getStatusCode();
        if ($status < 200 || $status > 299) {
            NotFoundModelException::throw("[HTTP code: $status] - Failed request to $url.");
        }

        $logger->request("[HTTP code: $status] - Request to $url finished successfully.");

        return new Address(Json::decode($response->getBody(), true));
    }
}
