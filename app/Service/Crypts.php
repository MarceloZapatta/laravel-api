<?php

namespace App\Service;

use Exception;
use Illuminate\Support\Facades\Auth;

class Crypts {
    /**
     * Base url of api
     * @var string
     */
    private $baseUrl = "https://www.mercadobitcoin.net/api/BTC/";

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this->baseUrl
        ]);
    }

    public function price()
    {
        $response = $this->client->get('ticker');

        return json_decode($response->getBody());
    }
}