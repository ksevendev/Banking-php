<?php

namespace Banking\Test;

use Banking\RequestHandler;
use PHPUnit\Framework\TestCase;

class RequestHandlerTest extends TestCase
{
    public function testBindApiKey()
    {
        $this->assertEquals(
            ['headers' => ['Authorization' => 'Basic a2F0aWF1Og==']],
            RequestHandler::bindApiKeyToBasicAuth([], 'katiau')
        );
    }
}
