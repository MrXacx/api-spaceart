<?php

namespace App\Services\Clients;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;

class PostalCodeClientService extends ClientService{

  private $uri = 'https://brasilapi.com.br/api/cep/v1';

  public static function make(): PostalCodeClientService {
    return new PostalCodeClientService();
  }
  public function get(string $postalCode): JsonResponse {

    $response = $this->client->get("$this->uri/$postalCode"); // Request
    $status = $response->getStatusCode();
    throw_if($status < 200 || $status > 299, new \Exception("Falha na consulta ao CEP. Código: $status"));
    
    return JsonResponse::fromJsonString($response->getBody()->getContents());
  }
}

?>