<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Exception;

use RuntimeException;
use function Safe\sprintf;

final class CannotParseUnknownValueType extends RuntimeException implements SnmpException
{
    public static function new(string $type) : self
    {
        return new self(sprintf('Encountered unknown value type "%s"', $type));
    }
}
