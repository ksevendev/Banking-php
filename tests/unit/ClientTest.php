<?php

namespace Banking\Test;

use Banking\Banking;
use Banking\Client;
use Banking\Exceptions\BankingException;
use Banking\Endpoints\Endpoint;
use Banking\Endpoints\Transactions;
use Banking\Endpoints\Customers;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

final class ClientTest extends TestCase
{
    public function testSuccessfulResponse()
    {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(200, [], '{"status":"Ok!"}'),
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        $client = new Client('apiKey', ['handler' => $handler]);

        $response = $client->request(Endpoint::POST, 'transactions');

        $this->assertEquals($response->status, "Ok!");
        $this->assertEquals(
            'api_key=apiKey',
            $container[0]['request']->getUri()->getQuery()
        );
    }

    /**
     * @expectedException \Banking\Exceptions\BankingException
     */
    public function testBankingFailedResponse()
    {
        $mock = new MockHandler([
            new Response(401, [], '{
                "errors": [{
                    "message": "api_key está faltando",
                    "parameter_name": "api_key",
                    "type": "invalid_parameter"
                }],
                "method": "get",
                "url": "/transactions"
            }')
        ]);

        $handler = HandlerStack::create($mock);

        $client = new Client('apiKey', ['handler' => $handler]);

        $errorType = 'invalid_parameter';
        $parameter = 'api_key';
        $message = 'api_key está faltando';
        $expectedExceptionMessage = sprintf(
            'ERROR TYPE: %s. PARAMETER: %s. MESSAGE: %s',
            $errorType,
            $parameter,
            $message
        );

        try {
            $response = $client->request(Endpoint::POST, 'transactions');
        } catch (\Banking\Exceptions\BankingException $exception) {
            $this->assertEquals($expectedExceptionMessage, $exception->getMessage());
            $this->assertEquals($parameter, $exception->getParameterName());
            $this->assertEquals($errorType, $exception->getType());

            throw $exception;
        }
    }

    /**
     * @expectedException \GuzzleHttp\Exception\ServerException
     */
    public function testRequestFailedResponse()
    {
        $mock = new MockHandler([
            new Response(502, [], '<div>Bad Gateway</div>')
        ]);

        $handler = HandlerStack::create($mock);

        $client = new Client('apiKey', ['handler' => $handler]);

        $response = $client->request(Endpoint::POST, 'transactions');
    }

    public function testSuccessfulResponseWithCustomUserAgentHeader()
    {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(200, [], '{"status":"Ok!"}'),
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        $client = new Client(
            'apiKey',
            [
                'handler' => $handler,
                'headers' => [
                  'User-Agent' => 'MyCustomApplication/10.2.2',
                  'X-Banking-Version' => '2017-07-17',
                  'Custom-Header' => 'header',
                ]
            ]
        );

        $response = $client->request(Endpoint::POST, 'transactions');

        $this->assertEquals($response->status, "Ok!");
        $this->assertEquals(
            'api_key=apiKey',
            $container[0]['request']->getUri()->getQuery()
        );

        $expectedUserAgent = sprintf(
            'MyCustomApplication/10.2.2 Banking-php/%s php/%s',
            Banking::VERSION,
            phpversion()
        );

        $this->assertEquals(
            '2017-07-17',
            $container[0]['request']->getHeaderLine('X-Banking-Version')
        );

        $this->assertEquals(
            'header',
            $container[0]['request']->getHeaderLine('Custom-Header')
        );

        $this->assertEquals(
            $expectedUserAgent,
            $container[0]['request']->getHeaderLine('User-Agent')
        );
        $this->assertEquals(
            $expectedUserAgent,
            $container[0]['request']->getHeaderLine(
                Client::Banking_USER_AGENT_HEADER
            )
        );
    }

    public function testTransactions()
    {
        $client = new Client('apiKey');

        $transactions = $client->transactions();

        $this->assertInstanceOf(Transactions::class, $transactions);
    }

    public function testCustomers()
    {
        $client = new Client('apiKey');

        $customers = $client->customers();

        $this->assertInstanceOf(Customers::class, $customers);
    }
}
