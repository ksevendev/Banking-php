<?php

namespace Banking\Endpoints;

use Banking\Client;
use Banking\Routes;
use Banking\Endpoints\Endpoint;

class Finance extends Endpoint
{

    /**
     * @return \ArrayObject
     */
    public function get()
    {
        return $this->client->request(
            self::GET,
            Routes::finance()->base()
        );
    }

    /**
     * @return \ArrayObject
     */
    public function invoicing()
    {
        return $this->client->request(
            self::GET,
            Routes::finance()->invoicing()
        );
    }

    /**
     * @return \ArrayObject
     */
    public function balance()
    {
        return $this->client->request(
            self::GET,
            Routes::finance()->balance()
        );
    }

    /**
     * @return \ArrayObject
     */
    public function cashout()
    {
        return $this->client->request(
            self::POST,
            Routes::finance()->cashout()
        );
    }

}
