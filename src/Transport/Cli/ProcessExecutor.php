<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Transport\Cli;

interface ProcessExecutor
{
    /** @param string[] $args */
    public function execute(array $args) : string;
}
