<?php

    $token = null; // "ktuXDHrbK2uOCUJUBaYmiCczwcvud1Rd";
    $pathInitialize = __DIR__ . "/../../../initialize.php";
    if (file_exists($pathInitialize)) {
        require_once $pathInitialize;
        $token = TOKEN;
    }

    return [

        'token' => $token,

        'base_url' => 'http://bank.test/api/',

        'userAgent' => 'Seven',

    ];
