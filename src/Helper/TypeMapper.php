<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Helper;

final class TypeMapper
{
    /**
     * @param iterable<string, string> $strings
     *
     * @return array<string, float>
     */
    public static function stringsToFloats(iterable $strings) : iterable
    {
        foreach ($strings as $key => $string) {
            yield $key => (float) $string;
        }
    }
}
