<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Transport;

use SimPod\PhpSnmp\Exception\EndOfMibReached;
use SimPod\PhpSnmp\Exception\GeneralException;
use SimPod\PhpSnmp\Exception\InvalidVersionProvided;
use SimPod\PhpSnmp\Exception\NoSuchInstanceExists;
use SimPod\PhpSnmp\Exception\NoSuchObjectExists;
use SNMP;
use Throwable;
use function strpos;
use const SNMP_OID_OUTPUT_NUMERIC;

final class ExtensionSnmpClient implements SnmpClient
{
    /** @var SNMP */
    private $snmp;

    public function __construct(
        string $host = '127.0.0.1',
        string $community = 'public',
        int $timeoutMs = 1000000,
        int $retry = 3,
        string $version = '2c',
        string $secLevel = 'noAuthNoPriv',
        string $authProtocol = 'MD5',
        string $authPassphrase = 'None',
        string $privProtocol = 'DES',
        string $privPassphrase = 'None'
    ) {
        switch ($version) {
            case '1':
                $snmpVersion = SNMP::VERSION_1;
                break;
            case '2c':
                $snmpVersion = SNMP::VERSION_2c;
                break;
            case '3':
                $snmpVersion = SNMP::VERSION_3;
                break;
            default:
                throw InvalidVersionProvided::new($version);
        }

        $this->snmp                     = new SNMP($snmpVersion, $host, $community, $timeoutMs, $retry);
        $this->snmp->oid_output_format  = SNMP_OID_OUTPUT_NUMERIC;
        $this->snmp->exceptions_enabled = SNMP::ERRNO_ANY;
        if ($snmpVersion !== SNMP::VERSION_3) {
            return;
        }

        $this->snmp->setSecurity($secLevel, $authProtocol, $authPassphrase, $privProtocol, $privPassphrase);
    }

    /** @inheritDoc */
    public function get(array $oids) : array
    {
        try {
            $output = $this->snmp->get($oids);
        } catch (Throwable $throwable) {
            throw $this->processException($throwable, $oids);
        }

        return $this->processOutput($output);
    }

    /** @inheritDoc */
    public function getNext(array $oids) : array
    {
        try {
            $output = $this->snmp->getnext($oids);
        } catch (Throwable $throwable) {
            throw $this->processException($throwable, $oids);
        }

        return $this->processOutput($output);
    }

    /** @inheritDoc */
    public function walk(string $oid, int $maxRepetitions = 40) : array
    {
        try {
            $output = $this->snmp->walk($oid, false, $maxRepetitions);
        } catch (Throwable $throwable) {
            throw $this->processException($throwable, [$oid]);
        }

        return $this->processOutput($output);
    }

    /**
     * @param array<string, string> $output
     *
     * @return array<string, mixed>
     */
    private function processOutput(array $output) : array
    {
        $result = [];
        foreach ($output as $oid => $value) {
            $result[$oid] = ValueParser::parse($value);
        }

        return $result;
    }

    /** @param list<string> $oids */
    private function processException(Throwable $throwable, array $oids) : Throwable
    {
        if (strpos($throwable->getMessage(), 'No Such Object') !== false) {
            return NoSuchObjectExists::fromThrowable($throwable);
        }

        if (strpos($throwable->getMessage(), 'No Such Instance') !== false
            || strpos($throwable->getMessage(), 'noSuchName') !== false) {
            return NoSuchInstanceExists::fromThrowable($throwable);
        }

        if (strpos($throwable->getMessage(), 'No more variables left in this MIB View') !== false) {
            return EndOfMibReached::fromThrowable($throwable);
        }

        return GeneralException::fromThrowable($throwable, $oids);
    }
}
