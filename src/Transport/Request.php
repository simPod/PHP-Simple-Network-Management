<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Transport;

final class Request
{
    private const GET = 'get';
    private const GET_NEXT = 'getNext';
    private const WALK = 'walk';

    /** @var string */
    public $type;

    /** @var list<string> */
    public $oids;

    /**
     * @param list<string> $oids
     */
    private function __construct(string $type, array $oids)
    {
        $this->type = $type;
        $this->oids = $oids;
    }

    public static function get(array $oids) : self
    {
        return new self(self::GET, $oids);
    }

    public static function getNext(array $oids) : self
    {
        return new self(self::GET_NEXT, $oids);
    }

    public static function walk(string $oid) : self
    {
        return new self(self::WALK, [$oid]);
    }
}
