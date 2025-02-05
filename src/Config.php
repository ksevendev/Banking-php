<?php

    namespace Banking;

    use Banking\Logger;

    class Config
    {
        private static ?Config $instance = null;

        private ?Logger $logger = null;

        public ?string $baseURL = 'https://bank.malipay.com.br/';

        public ?string $userAgent = 'KSeven';

        // Impede instância externa
        private function __construct()
        {
            $this->logger = new Logger("BankingClient"); // Inicializa o Logger
            if ($this->baseURL === null) {
                $this->logger->error('Oops, a BaseURL é obrigatória.');
            }
        }

        public static function getInstance(): Config
        {
            if (self::$instance === null) {
                self::$instance = new Config();
            }
            return self::$instance;
        }

    }
