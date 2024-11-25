<?php

namespace Banking\Endpoints;

use Banking\Client;

abstract class Endpoint
{
    /**
     * @var string
     */
    const POST = 'POST';
    /**
     * @var string
     */
    const GET = 'GET';
    /**
     * @var string
     */
    const PUT = 'PUT';
    /**
     * @var string
     */
    const DELETE = 'DELETE';

    /**
     * @var \Banking\Client
     */
    protected $client;

    /**
     * @param \Banking\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
