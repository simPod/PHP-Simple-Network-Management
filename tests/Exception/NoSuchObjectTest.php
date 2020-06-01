<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Tests\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use SimPod\PhpSnmp\Exception\NoSuchObjectExists;

final class NoSuchObjectTest extends TestCase
{
    public function testFromThrowable() : void
    {
        $exception = new Exception("Error in packet at '.1.4': No Such Object available on this agent at this OID");

        $this->expectException(NoSuchObjectExists::class);
        $this->expectExceptionMessage('No Such Object available on this agent at this OID: .1.4');

        throw NoSuchObjectExists::fromThrowable($exception);
    }

    public function testFromThrowableWithUnexpectedMessage() : void
    {
        $exception = new Exception('unexpected message');

        $this->expectException(NoSuchObjectExists::class);
        $this->expectExceptionMessage('No Such Object available on this agent at this OID');

        throw NoSuchObjectExists::fromThrowable($exception);
    }
}
