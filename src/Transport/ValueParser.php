<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Transport;

use SimPod\PhpSnmp\Exception\CannotParseUnknownValueType;
use function explode;
use function Safe\substr;
use function str_replace;
use function strrpos;
use function trim;

final class ValueParser
{
    /** @return int|string */
    public static function parse(string $rawValue)
    {
        if ($rawValue === '""') {
            return '';
        }

        [$type, $value] = explode(': ', $rawValue, 2);

        $value = trim($value);

        switch ($type) {
            case 'Counter64':
            case 'IpAddress':
            case 'OID':
                return $value;
            case 'Hex-STRING':
                return str_replace("\n", '', $value);
            case 'STRING':
                return substr($value, 1, -1);
            case 'INTEGER':
            case 'Counter32':
            case 'Gauge32':
                return (int) $value;
            case 'Timeticks':
                return (int) substr($value, 1, (int) strrpos($value, ')') - 1);
        }

        throw CannotParseUnknownValueType::new($type);
    }
}
