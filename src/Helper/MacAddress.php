<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Helper;

use function bin2hex;
use function count;
use function explode;
use function implode;
use function preg_match;
use function sprintf;
use function str_pad;
use function strlen;
use function strtolower;
use const STR_PAD_LEFT;

class MacAddress
{
    private const DEFAULT_DELIMITER = ':';
    private const REGEX             = '[[:xdigit:]]{1,4}(?<delim>[:. -])(?:[[:xdigit:]]{1,4}\g{delim}){1,4}[[:xdigit:]]{1,4}';

    public static function normalize(string $macAddress) : ?string
    {
        if (strlen($macAddress) === 6) {
            $macAddress = bin2hex($macAddress);
            $formattedMacAddress = '';
            for ($i = 0; $i < 12; $i++) {
                $formattedMacAddress .= $macAddress[$i];
                if ($i % 2 !== 1 || $i === 11) {
                    continue;
                }

                $formattedMacAddress .= self::DEFAULT_DELIMITER;
            }

            return $formattedMacAddress;
        }

        if (preg_match(sprintf('~^%s$~', self::REGEX), $macAddress, $matches) !== 1) {
            return null;
        }

        /** @var string[] $parts */
        $parts      = explode($matches['delim'], strtolower($macAddress));
        $partsCount = count($parts);

        if ($partsCount === 3) {
            return self::processThreeDoubleOctets($parts);
        }

        if ($partsCount !== 6) {
            return null;
        }

        foreach ($parts as $i => $part) {
            $parts[$i] = str_pad($part, 2, '0', STR_PAD_LEFT);
        }

        return implode(self::DEFAULT_DELIMITER, $parts);
    }

    /**
     * @param iterable<string,string> $macAddresses
     *
     * @return iterable<string,string>
     */
    public static function normalizeBulk(iterable $macAddresses) : iterable
    {
        foreach ($macAddresses as $key => $macAddress) {
            $macAddress = self::normalize($macAddress);
            if ($macAddress === null) {
                continue;
            }

            yield $key => $macAddress;
        }
    }

    /**
     * @param string[] $threeDoubleOctets
     */
    private static function processThreeDoubleOctets(array $threeDoubleOctets) : string
    {
        $sixOctets = [];
        foreach ($threeDoubleOctets as $doubleOctet) {
            $doubleOctet = str_pad($doubleOctet, 4, '0', STR_PAD_LEFT);
            $sixOctets[] = $doubleOctet[0] . $doubleOctet[1];
            $sixOctets[] = $doubleOctet[2] . $doubleOctet[3];
        }

        return implode(self::DEFAULT_DELIMITER, $sixOctets);
    }
}
