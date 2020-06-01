<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Tests\Transport\Cli;

use PHPUnit\Framework\TestCase;
use SimPod\PhpSnmp\Exception\GeneralException;
use SimPod\PhpSnmp\Transport\Cli\SymfonyProcessProcessExecutor;
use function bin2hex;
use function microtime;
use function random_bytes;
use function Safe\sprintf;

final class SymfonyProcessProcessExecutorTest extends TestCase
{
    public function testError() : void
    {
        $command = bin2hex(random_bytes(100));

        $this->expectExceptionObject(GeneralException::new(sprintf('sh: 1: exec: %s: not found', $command)));

        $executor = new SymfonyProcessProcessExecutor(1);
        $executor->execute([$command]);
    }

    public function testTimeout() : void
    {
        $this->expectExceptionObject(
            GeneralException::new(sprintf('The process "%s" exceeded the timeout of 1 seconds', "'sleep' '5'"))
        );

        $time = microtime(true);

        $executor = new SymfonyProcessProcessExecutor(1);
        $executor->execute(['sleep', '5']);

        self::assertLessThan(2, microtime(true) - $time);
    }
}
