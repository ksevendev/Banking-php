<?php

namespace Banking\Endpoints;

use Banking\Client;
use Banking\Routes;
use Banking\Endpoints\Endpoint;

class Setting extends Endpoint
{

    /**
     * @return \ArrayObject
     */
    public function payment()
    {
        return $this->client->request(
            self::GET,
            Routes::setting()->base()
        );
    }

}
