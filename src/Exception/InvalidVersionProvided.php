<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Exception;

use RuntimeException;
use function Safe\sprintf;

final class InvalidVersionProvided extends RuntimeException implements SnmpException
{
    public static function new(string $version) : self
    {
        return new self(sprintf('Invalid or unsupported SNMP version "%s"', $version));
    }
}
