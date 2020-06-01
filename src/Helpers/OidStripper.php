<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Helpers;

use SimPod\PhpSnmp\Exception\GeneralException;
use SimPod\PhpSnmp\Transport\SnmpClient;
use function array_key_first;
use function Safe\substr;
use function strlen;
use function strrpos;

final class OidStripper
{
    /**
     * @param array<string, mixed> $leafOidData
     *
     * @return array<int, mixed>
     *
     * @psalm-template T
     * @psalm-param array<string, T> $leafOidData
     * @psalm-return array<int, T>
     */
    public static function stripParent(array $leafOidData) : array
    {
        $firstKey = array_key_first($leafOidData);
        if ($firstKey === null) {
            throw GeneralException::new('Expected non-empty array');
        }

        $lastDotPos = strrpos($firstKey, '.');
        if ($lastDotPos === false) {
            throw GeneralException::new('Expected keys to be full OIDs');
        }

        $stripLength = $lastDotPos + 1;

        $result = [];
        foreach ($leafOidData as $oid => $value) {
            $result[(int) substr($oid, $stripLength)] = $value;
        }

        return $result;
    }

    /** @return array<string, mixed> */
    public static function walk(SnmpClient $snmpClient, string $oid, int $maxRepetitions = 40) : array
    {
        $stripLength = strlen($oid) + 1;

        $result = [];
        foreach ($snmpClient->walk($oid, $maxRepetitions) as $childOid => $value) {
            $result[substr($childOid, $stripLength)] = $value;
        }

        return $result;
    }
}
