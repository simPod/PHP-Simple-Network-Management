<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Tests\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use SimPod\PhpSnmp\Exception\EndOfMibReached;

final class EndOfMibTest extends TestCase
{
    public function testFromThrowable() : void
    {
        $exception = new Exception(
            "Error in packet at '.1.4': No more variables left in this MIB View (It is past the end of the MIB tree)"
        );

        $this->expectException(EndOfMibReached::class);
        $this->expectExceptionMessage(
            'No more variables left in this MIB View (It is past the end of the MIB tree), tried oid: .1.4'
        );

        throw EndOfMibReached::fromThrowable($exception);
    }

    public function testFromThrowableWithUnexpectedMessage() : void
    {
        $exception = new Exception('unexpected message');

        $this->expectException(EndOfMibReached::class);
        $this->expectExceptionMessage('No more variables left in this MIB View (It is past the end of the MIB tree)');

        throw EndOfMibReached::fromThrowable($exception);
    }
}
