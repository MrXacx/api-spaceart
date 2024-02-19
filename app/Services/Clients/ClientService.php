<?php

namespace App\Services\Clients;

use GuzzleHttp\Client;

abstract class ClientService
{
    protected Client $client;

    abstract public static function make(): ClientService;

    public function __construct()
    {
        $this->client = new Client;
    }
}
