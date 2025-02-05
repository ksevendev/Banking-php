<?php

    namespace Banking;

    use Banking\Log\Logger AS RunLogger;
    use Banking\Log\Handler\StreamHandler;

    class Logger 
    {

        /**
         * @var bool $debug
         */
        public bool $debug = true;

        /**
         * @var RunLogger $logger
         */
        private $logger;

        public function __construct(string $name, array $handlers = [])
        {
            $this->logger = new RunLogger($name);
            if ($handlers){
                $this->logger->setHandlers($handlers);
            }
            $logFile = __DIR__ . '/../../../../logs/' . date("d-m-Y") . '.log';
            $this->logger->pushHandler(new StreamHandler($logFile, RunLogger::DEBUG));

            $this->logger->info('Log de informações iniciado.');
        }

        public function log($level, string|\Stringable $message, array $context = [])
        {
            return $this->logger->log($level, $message, $context);
        }

        public function debug(string|\Stringable $message, array $context = [])
        {
            return $this->logger->debug($message, $context);
        }

        public function info(string|\Stringable $message, array $context = [])
        {
            return $this->logger->info($message, $context);
        }

        public function notice(string|\Stringable $message, array $context = [])
        {
            return $this->logger->notice($message, $context);
        }

        public function warning(string|\Stringable $message, array $context = [])
        {
            return $this->logger->warning($message, $context);
        }

        public function error(string|\Stringable $message, array $context = [])
        {
            return $this->logger->error($message, $context);
        }

        public function critical(string|\Stringable $message, array $context = [])
        {
            return $this->logger->critical($message, $context);
        }

        public function alert(string|\Stringable $message, array $context = [])
        {
            return $this->logger->alert($message, $context);
        }

        public function emergency(string|\Stringable $message, array $context = [])
        {
            return $this->logger->emergency($message, $context);
        }

    }
