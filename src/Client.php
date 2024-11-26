<?php

namespace Banking;

use Banking\Banking;
use Banking\RequestHandler;
use Banking\ResponseHandler;

use Banking\Endpoints\Manager;
use Banking\Endpoints\User;
use Banking\Endpoints\Finance;
use Banking\Endpoints\Sell;
use Banking\Endpoints\Setting;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Banking\Exceptions\InvalidJsonException;

class Client
{
    /**
     * @var string Token da API
     */
    private static $token;

    /**
     * @var string Base URI da API
     */
    private static $baseUri;

    /**
     * @var string Header usado para identificar requisições da aplicação
     */
    private static $bankingUserAgentHeader;

    /**
     * @var \GuzzleHttp\Client
     */
    private $http;

    /**
     * @var string Chave da API
     */
    private $apiKey;

    /**
     * @var \Banking\Endpoints\Manager
     */
    private $manager;

    /**
     * @var \Banking\Endpoints\User
     */
    private $user;
    
    /**
     * @var \Banking\Endpoints\Finance
     */
    private $finance;
    
    /**
     * @var \Banking\Endpoints\Sell
     */
    private $sell;

    /**
     * @var \Banking\Endpoints\Setting
     */
    private $setting;

    /**
     * Construtor da classe Client
     *
     * @param string $apiKey
     * @param array|null $extras Configurações adicionais
     */
    public function __construct(string $apiKey = null, array $extras = null)
    {
        // Carrega o arquivo de configuração
        $config = require __DIR__ . '/config.php';

        // Define valores estáticos a partir da configuração
        self::$token = $config['token'] ?? '';
        self::$baseUri = $config['base_url'] ?? '';
        self::$bankingUserAgentHeader = $config['userAgent'] ?? 'Default-Banking-User-Agent';

        $this->apiKey = $apiKey ?? self::$token;

        $options = ['base_uri' => self::$baseUri];

        if (!is_null($extras)) {
            $options = array_merge($options, $extras);
        }

        $domain = $_SERVER['HTTP_HOST'];
        if ($domain) {
            $options['headers']['domain'] = $domain; 
        }

        $userAgent = isset($options['headers']['User-Agent']) ? $options['headers']['User-Agent'] : '';

        $options['headers']['User-Agent'] = $this->addUserAgentHeaders($userAgent);
        $options['headers']['X-Banking-User-Agent'] = self::$bankingUserAgentHeader;

        $this->http = new HttpClient($options);

        $this->manager = new Manager($this);
        $this->user = new User($this);
        $this->finance = new Finance($this);
        $this->sell = new Sell($this);
        $this->setting = new Setting($this);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     *
     * @throws \Banking\Exceptions\BankingException
     * @return \ArrayObject
     *
     * @psalm-suppress InvalidNullableReturnType
     */
    public function request($method, $uri, $options = [])
    {
        try {
            $response = $this->http->request(
                $method,
                $uri,
                RequestHandler::bindApiKeyToBasicAuth(
                    $options,
                    $this->apiKey
                )
            );

            return ResponseHandler::success((string)$response->getBody());
        } catch (InvalidJsonException $exception) {
            throw $exception;
        } catch (ClientException $exception) {
            ResponseHandler::failure($exception);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Build an user-agent string to be informed on requests
     *
     * @param string $customUserAgent
     *
     * @return string
     */
    private function buildUserAgent($customUserAgent = '')
    {
        return trim(sprintf(
            '%s Banking-php/%s php/%s',
            $customUserAgent,
            Banking::VERSION,
            phpversion()
        ));
    }

    /**
     * Append new keys (the default and Banking) related to user-agent
     *
     * @param string $customUserAgent
     * @return string
     */
    private function addUserAgentHeaders($customUserAgent = '')
    {
        return $this->buildUserAgent($customUserAgent);
    }

    /**
     * @return \Banking\Endpoints\Manager
     */
    public function manager()
    {
        return $this->manager;
    }

    /**
     * @return \Banking\Endpoints\User
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * @return \Banking\Endpoints\Finance
     */
    public function finance()
    {
        return $this->finance;
    }

    /**
     * @return \Banking\Endpoints\Sell
     */
    public function sell()
    {
        return $this->sell;
    }

    /**
     * @return \Banking\Endpoints\Setting
     */
    public function setting()
    {
        return $this->setting;
    }

}
