<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp;

use function sprintf;

final class OidWithIndex
{
    /** @var string */
    private $oid;

    /** @var int */
    private $index;

    public function __construct(string $oid, int $index)
    {
        $this->oid   = $oid;
        $this->index = $index;
    }

    public static function new(string $oid, int $index) : self
    {
        return new self($oid, $index);
    }

    public function __toString() : string
    {
        return sprintf('%s.%d', $this->oid, $this->index);
    }
}
