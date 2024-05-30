<?php

namespace App\Repositories\Contracts;

use GuzzleHttp\Client;

abstract class WebClientRepository
{
    protected Client $client;

    private string $uri;

    abstract public static function make(): WebClientRepository;

    public function __construct()
    {
        $this->client = new Client;
    }
}
