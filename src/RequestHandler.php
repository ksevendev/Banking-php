<?php

namespace Banking;

class RequestHandler
{
    /**
     * @param array $options
     * @param string $apiKey
     *
     * @return array
     */
    public static function bindApiKeyToBasicAuth(array $options, $apiKey)
    {
        $options['headers']['Token'] =  $apiKey;

        return $options;
    }
}
