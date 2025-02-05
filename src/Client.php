<?php

namespace Banking;

use Banking\Config;
use Banking\Banking;
use Banking\Logger;
use Banking\RequestHandler;
use Banking\ResponseHandler;

use Banking\Exceptions\InvalidJsonException;

use Banking\Endpoints\Manager;
use Banking\Endpoints\User;
use Banking\Endpoints\Finance;
use Banking\Endpoints\Sell;
use Banking\Endpoints\Setting;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;

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
     * @var Logger
     */
    private $logger;

    /**
     * Construtor da classe Client
     *
     * @param string $apiKey
     * @param array|null $extras Configurações adicionais
     */
    public function __construct(string $apiKey, array $extras = null)
    {

        // Configuração do Logger
        $this->logger = new Logger('BankingClient');
        $this->logger->info('Client iniciado.');

        // Carrega as configurações
        $getConfig = Config::getInstance();

        $config = [ 
            'token' => $apiKey,
            'base_url' => $getConfig->baseURL,
            'userAgent' => $getConfig->userAgent,
        ];

        // Define valores estáticos a partir da configuração
        self::$token = $config['token'];
        $this->apiKey = self::$token; // Se $apiKey for nulo, usa o token global
        self::$baseUri = rtrim($config['base_url'], '/'); // Garante a barra no final
        self::$bankingUserAgentHeader = $config['userAgent'];


        // Garante que a URL base comece com HTTPS e termine com "/api/"
        if (!str_starts_with(self::$baseUri, 'https://')) {
            self::$baseUri = str_replace('http://', 'https://', self::$baseUri);
        }

        // Se a URL não terminar com "/api/", adiciona automaticamente
        if (!str_ends_with(self::$baseUri, '/api/')) {
            self::$baseUri = rtrim(self::$baseUri, '/') . '/api/';
        }

        // Inicializa as opções do HttpClient
        $options = [
            'base_uri' => self::$baseUri,
            'verify'   => false, // ⚠ Desativa verificação SSL (útil para testes, mas ative em produção)
        ];

    
        if (!is_null($extras)) {
            $options = array_merge($options, $extras);
        }
    
        $domain = $_SERVER['HTTP_HOST'];
        if ($domain) {
            $options['headers']['domain'] = $domain; 
        }
    
        // Configura o User-Agent
        $userAgent = isset($options['headers']['User-Agent']) ? $options['headers']['User-Agent'] : '';
        $options['headers']['User-Agent'] = $this->addUserAgentHeaders($userAgent);
        $options['headers']['X-Banking-User-Agent'] = self::$bankingUserAgentHeader;

        $options['headers']['Content-Type'] = 'application/json';
        $options['headers']['Accept'] = 'application/json';

        $options['headers']['Token'] = $this->apiKey;
    
        // Tenta inicializar o HttpClient com HTTPS
        try {
            $this->http = new HttpClient($options);
            // Se funcionar com HTTPS, continue
        } catch (\Exception $exception) {
            // Se falhar com HTTPS, tenta com HTTP
            $httpUri = str_replace("https://", "http://", self::$baseUri); // Altera para HTTP
            $options['base_uri'] = $httpUri;  // Atualiza para HTTP
    
            // Tenta novamente com HTTP
            $this->http = new HttpClient($options);
        }
    
        // Inicializa os outros componentes
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
    
            $this->logger->info("Respostas recebidas: ", [
                'response' => $response,
                'body' => (string)$response->getBody(),
            ]);
    
            return ResponseHandler::success((string)$response->getBody());
        } catch (InvalidJsonException $exception) {
            $this->logger->error("1 - Erro na requisição: " . $exception->getMessage());
            throw $exception;
        } catch (ClientException $exception) {
            $this->logger->error("2 - Erro na requisição: " . $exception->getMessage());
            ResponseHandler::failure($exception);
        } catch (\Exception $exception) {
            $this->logger->error("3 - Erro na requisição: " . $exception->getMessage());
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
     * @return string
     */
    public function getUrl()
    {
        return str_replace("api/", "", self::$baseUri);
    }

    /**
     * @return string
     */
    public function getUrlBase()
    {
        return self::$baseUri;
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
