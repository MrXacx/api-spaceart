<?php

namespace App\Repositories;

use App\Exceptions\NotFoundException;
use App\Models\Address;
use App\Repositories\Contracts\WebClientRepository;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;

class PostalCodeRepository extends WebClientRepository
{
    private $uri = 'https://brasilapi.com.br/api/cep/v1';

    public static function make(): PostalCodeRepository
    {
        return new PostalCodeRepository;
    }

    /**
     * @throws GuzzleException
     * @throws NotFoundException
     */
    public function fetch(string $postalCode): Address
    {
        $response = $this->client->get("$this->uri/$postalCode"); // Request

        $status = $response->getStatusCode();
        if ($status < 200 || $status > 299) {
            NotFoundException::throw("[HTTP code: $status] - Failed request to $this->uri/$postalCode.");
        }

        $responseBody = JsonResponse::fromJsonString($response->getBody()->getContents());

        return new Address((array) $responseBody);
    }
}
