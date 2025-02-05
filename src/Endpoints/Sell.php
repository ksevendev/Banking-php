<?php

namespace Banking\Endpoints;

use Banking\Client;
use Banking\Routes;
use Banking\Endpoints\Endpoint;

class Sell extends Endpoint
{

    /**
     * @return \ArrayObject
     */
    public function get()
    {
        return $this->client->request(
            self::GET,
            Routes::sell()->base()
        );
    }

    /**
     * @param array $payload
     *
     * @return \ArrayObject
     */
    public function create(array $payload)
    {
        return $this->client->request(
            self::POST,
            Routes::sell()->base(),
            ['json' => $payload]
        );
    }

    /**
     * @param array $payload
     * 
     * @return \ArrayObject
     */
    public function getOrder(array $payload)
    {
        return $this->client->request(
            self::GET,
            Routes::sell()->getOrder($payload['id'])
        );
    }
    
    /**
     * @return \ArrayObject
     */
    public function getOrders(array $payload)
    {
        return $this->client->request(
            self::GET,
            Routes::sell()->base(),
            ['query' => $payload]
        );
    }

}
