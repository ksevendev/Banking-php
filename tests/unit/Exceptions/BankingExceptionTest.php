<?php

namespace Banking\Test;

use Banking\Exceptions\BankingException;
use PHPUnit\Framework\TestCase;

final class BankingExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function buildExceptionMessage()
    {
        $exception = new BankingException(
            'InvalidType',
            'items',
            'value must be array'
        );
        $errorType = 'InvalidType';
        $parameter = 'items';
        $message = 'value must be array';

        $expectedMessage = sprintf(
            'ERROR TYPE: %s. PARAMETER: %s. MESSAGE: %s',
            $errorType,
            $parameter,
            $message
        );
        $this->assertEquals($expectedMessage, $exception->getMessage());
    }
}
