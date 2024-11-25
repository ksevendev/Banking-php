<?php

namespace Banking\Endpoints;

use Banking\Client;
use Banking\Routes;
use Banking\Endpoints\Endpoint;

class Manager extends Endpoint
{

    /**
     * @return \ArrayObject
     */
    public function get()
    {
        return $this->client->request(
            self::POST,
            Routes::manager()->base()
        );
    }

    /**
     * @return \ArrayObject
     */
    public function connect()
    {
        return $this->client->request(
            self::POST,
            Routes::manager()->connect()
        );
    }

    /**
     * @return \ArrayObject
     */
    public function disconnect()
    {
        return $this->client->request(
            self::POST,
            Routes::manager()->disconnect()
        );
    }

    /**
     * @return \ArrayObject
     */
    public function changeStatus()
    {
        return $this->client->request(
            self::POST,
            Routes::manager()->changeStatus()
        );
    }

}
