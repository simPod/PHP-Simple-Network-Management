<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Helpers;

use SimPod\PhpSnmp\Exception\GeneralException;
use SimPod\PhpSnmp\Exception\NoSuchInstanceExists;
use SimPod\PhpSnmp\Transport\SnmpClient;
use function array_shift;
use function array_values;
use function strpos;

final class ValueGetter
{
    /**
     * @param array<int|string, mixed> $raw
     *
     * @return mixed
     *
     * @psalm-template T
     * @psalm-param array<int|string, T> $raw
     * @psalm-return T
     */
    public static function first(array $raw)
    {
        $result = array_shift($raw);
        if ($result === null) {
            throw GeneralException::new('Expected non-empty array');
        }

        return $result;
    }

    /** @return mixed */
    public static function firstFromSameTree(SnmpClient $snmpClient, string $oid)
    {
        return self::first(self::firstFromSameTrees($snmpClient, [$oid]));
    }

    /**
     * @param list<string> $oids
     *
     * @return list<mixed>
     */
    public static function firstFromSameTrees(SnmpClient $snmpClient, array $oids) : array
    {
        $result = $snmpClient->getNext($oids);

        $i = 0;
        foreach ($result as $oid => $value) {
            if (strpos($oid, $oids[$i]) !== 0) {
                throw NoSuchInstanceExists::fromOid($oids[$i]);
            }

            $i++;
        }

        return array_values($result);
    }
}
