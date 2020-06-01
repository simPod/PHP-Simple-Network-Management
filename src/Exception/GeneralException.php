<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Exception;

use RuntimeException;
use Throwable;
use function implode;
use function Safe\sprintf;

// phpcs:ignore SlevomatCodingStandard.Classes.SuperfluousExceptionNaming.SuperfluousSuffix
final class GeneralException extends RuntimeException implements SnmpException
{
    /** @param list<string> $oids */
    public static function new(string $error, ?Throwable $previous = null, ?array $oids = null) : self
    {
        if ($oids !== null) {
            $error .= sprintf(', oids: %s', implode(', ', $oids));
        }

        return new self($error, 0, $previous);
    }

    /** @param list<string> $oids */
    public static function fromThrowable(Throwable $throwable, ?array $oids = null) : self
    {
        return self::new($throwable->getMessage(), $throwable, $oids);
    }
}
