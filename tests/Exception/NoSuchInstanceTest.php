<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Tests\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use SimPod\PhpSnmp\Exception\NoSuchInstanceExists;

final class NoSuchInstanceTest extends TestCase
{
    public function testFromThrowable() : void
    {
        $exception = new Exception("Error in packet at '.1.4': No Such Instance currently exists at this OID");

        $this->expectException(NoSuchInstanceExists::class);
        $this->expectExceptionMessage('No Such Instance currently exists at this OID: .1.4');

        throw NoSuchInstanceExists::fromThrowable($exception);
    }

    public function testFromThrowableWithUnexpectedMessage() : void
    {
        $exception = new Exception('unexpected message');

        $this->expectException(NoSuchInstanceExists::class);
        $this->expectExceptionMessage('No Such Instance currently exists at this OID');

        throw NoSuchInstanceExists::fromThrowable($exception);
    }
}
