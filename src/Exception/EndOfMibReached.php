<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Exception;

use RuntimeException;
use Throwable;
use function Safe\preg_match;
use function Safe\sprintf;

final class EndOfMibReached extends RuntimeException implements SnmpException
{
    public static function new() : self
    {
        return new self('No more variables left in this MIB View (It is past the end of the MIB tree)');
    }

    public static function fromOid(string $oid) : self
    {
        return new self(
            sprintf(
                'No more variables left in this MIB View (It is past the end of the MIB tree), tried oid: %s',
                $oid
            )
        );
    }

    public static function fromThrowable(Throwable $throwable) : self
    {
        if (preg_match("~Error in packet at '(.+?)':~", $throwable->getMessage(), $matches) !== 1) {
            throw self::new();
        }

        return self::fromOid($matches[1]);
    }
}
