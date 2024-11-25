<?php

namespace Banking\Endpoints;

use Banking\Client;
use Banking\Routes;
use Banking\Endpoints\Endpoint;

class User extends Endpoint
{

    /**
     * @return \ArrayObject
     */
    public function get()
    {
        return $this->client->request(
            self::POST,
            Routes::user()->base()
        );
    }

    /**
     * @return \ArrayObject
     */
    public function auth()
    {
        return $this->client->request(
            self::POST,
            Routes::user()->auth()
        );
    }

    /**
     * @return \ArrayObject
     */
    public function authBasic()
    {
        return $this->client->request(
            self::GET,
            Routes::user()->auth()
        );
    }

}
