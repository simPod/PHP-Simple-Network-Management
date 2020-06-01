<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Transport\Cli;

use SimPod\PhpSnmp\Exception\GeneralException;
use SimPod\PhpSnmp\Exception\SnmpException;
use Symfony\Component\Process\Process;
use Throwable;
use function trim;

final class SymfonyProcessProcessExecutor implements ProcessExecutor
{
    /** @var int */
    private $commandTimeout;

    public function __construct(int $commandTimeout)
    {
        $this->commandTimeout = $commandTimeout;
    }

    /** @inheritDoc */
    public function execute(array $args) : string
    {
        try {
            $process = new Process($args);
            $process->setTimeout($this->commandTimeout);
            $process->run();

            if (! $process->isSuccessful()) {
                throw GeneralException::new(trim($process->getErrorOutput()));
            }

            return $process->getOutput();
        } catch (Throwable $throwable) {
            if ($throwable instanceof SnmpException) {
                throw $throwable;
            }

            throw GeneralException::fromThrowable($throwable);
        }
    }
}
