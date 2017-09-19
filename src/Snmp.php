<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp;

use SimPod\PhpSnmp\Exception\SnmpFailed;
use SimPod\PhpSnmp\Mib\MibBase;
use const PREG_OFFSET_CAPTURE;
use const SNMP_OID_OUTPUT_NUMERIC;
use function array_key_exists;
use function assert;
use function explode;
use function is_int;
use function is_numeric;
use function preg_match;
use function snmp2_real_walk;
use function snmp3_real_walk;
use function snmp_set_oid_output_format;
use function snmprealwalk;
use function strpos;
use function strrev;
use function strrpos;
use function substr;
use function trim;

class Snmp
{
    /** @var string */
    private $community;

    /** @var string */
    private $host;

    /** @var string */
    private $version;

    /** @var string */
    private $secName;

    /** @var string */
    private $secLevel;

    /** @var string */
    private $authProtocol;

    /** @var string */
    private $authPassphrase;

    /** @var string */
    private $privProtocol;

    /** @var string */
    private $privPassphrase;

    /** @var int */
    private $retry;

    /** @var int */
    private $timeout;

    /** @var mixed */
    private $lastResult;

    public function __construct(
        string $host = '127.0.0.1',
        string $community = 'public',
        int $timeout = 1000000,
        int $retry = 5,
        string $version = '2c',
        string $secLevel = 'noAuthNoPriv',
        string $authProtocol = 'MD5',
        string $authPassphrase = 'None',
        string $privProtocol = 'DES',
        string $privPassphrase = 'None'
    ) {
        $this->community = $community;
        $this->host      = $host;
        $this->retry     = $retry;
        $this->timeout   = $timeout;
        $this->version   = $version;

        $this->secName        = $community;
        $this->secLevel       = $secLevel;
        $this->authProtocol   = $authProtocol;
        $this->authPassphrase = $authPassphrase;
        $this->privProtocol   = $privProtocol;
        $this->privPassphrase = $privPassphrase;
        snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
    }

    /**
     * @return mixed
     */
    public function walkFirstDegree(string $walkedOid)
    {
        $this->lastResult = $this->realWalk($walkedOid);

        if ($this->lastResult === false) {
            throw new SnmpFailed('Could not perform walk for OID ' . $walkedOid);
        }

        $result = [];

        $oidPrefix = null;
        foreach ($this->lastResult as $oid => $value) {
            $length = strrpos($oid, '.');
            assert(is_int($length));

            if ($oidPrefix !== null && $oidPrefix !== substr($oid, 0, $length)) {
                throw new SnmpFailed('Requested OID tree is not a first degree indexed SNMP value');
            }

            $oidPrefix = substr($oid, 0, $length);

            $result[substr($oid, $length + 1)] = $this->parseSnmpValue($value);
        }

        return $result;
    }

    /**
     * @return mixed[]|false
     */
    public function realWalk(string $oid)
    {
        switch ($this->getVersion()) {
            case '1':
                $this->lastResult = @snmprealwalk(
                    $this->host,
                    $this->community,
                    $oid,
                    $this->getTimeout(),
                    $this->getRetry()
                );
                break;
            case '2c':
                $this->lastResult = @snmp2_real_walk(
                    $this->host,
                    $this->community,
                    $oid,
                    $this->getTimeout(),
                    $this->getRetry()
                );
                break;
            case '3':
                $this->lastResult = @snmp3_real_walk(
                    $this->host,
                    $this->getSecName(),
                    $this->getSecLevel(),
                    $this->getAuthProtocol(),
                    $this->getAuthPassphrase(),
                    $this->getPrivProtocol(),
                    $this->getPrivPassphrase(),
                    $oid,
                    $this->getTimeout(),
                    $this->getRetry()
                );
                break;
            default:
                throw new SnmpFailed('Invalid SNMP version: ' . $this->getVersion());
        }

        return $this->lastResult;
    }

    private function getVersion() : string
    {
        return $this->version;
    }

    private function getTimeout() : int
    {
        return $this->timeout;
    }

    private function getRetry() : int
    {
        return $this->retry;
    }

    public function getSecName() : string
    {
        return $this->secName;
    }

    public function getSecLevel() : string
    {
        return $this->secLevel;
    }

    public function getAuthProtocol() : string
    {
        return $this->authProtocol;
    }

    public function getAuthPassphrase() : string
    {
        return $this->authPassphrase;
    }

    public function getPrivProtocol() : string
    {
        return $this->privProtocol;
    }

    public function getPrivPassphrase() : string
    {
        return $this->privPassphrase;
    }

    /**
     * @return int|string
     */
    private function parseSnmpValue(string $rawValue)
    {
        if ($rawValue === '""' || $rawValue === '') {
            return '';
        }

        [$type, $value] = explode(':', $rawValue, 2);

        $value = trim($value);

        switch ($type) {
            case 'STRING':
                $resolvedValue = (strpos($value, '"') === 0 ? trim(substr(substr($value, 1), 0, -1)) : $value);
                break;

            case 'INTEGER':
                if (is_numeric($value)) {
                    $resolvedValue = (int) $value;
                } else {
                    // find the first digit and offset the string to that point
                    // just in case there is some mib strangeness going on
                    preg_match('/\d/', $value, $m, PREG_OFFSET_CAPTURE);
                    $resolvedValue = (int) substr($value, $m[0][1]);
                }
                break;

            case 'Counter32':
                $resolvedValue = (int) $value;
                break;

            case 'Counter64':
                $resolvedValue = $value;
                break;

            case 'Gauge32':
                $resolvedValue = (int) $value;
                break;

            case 'Hex-STRING':
                $resolvedValue = $value;
                break;

            case 'IpAddress':
                $resolvedValue = $value;
                break;

            case 'OID':
                $objectTypes       = MibBase::OBJECT_TYPES;
                $reversedOidParts  = explode('.', strrev($value), 2);
                $objectTypeOidBase = strrev($reversedOidParts[1]);
                $resolvedValue     = array_key_exists(
                    $objectTypeOidBase,
                    $objectTypes
                ) ? (int) $reversedOidParts[0] : $value;
                break;

            case 'Timeticks':
                $length = strrpos($value, ')');
                assert(is_int($length));
                $resolvedValue = (int) substr($value, 1, $length - 1);
                break;

            default:
                throw new SnmpFailed('ERR: Unhandled SNMP return type: ' . $type);
        }

        return $resolvedValue;
    }

    /**
     * @return mixed[]
     */
    public function walk(string $oid) : array
    {
        $rawResult = $this->realWalk($oid);
        if ($rawResult === false) {
            throw new SnmpFailed('Could not perform walk for OID ' . $oid);
        }
        $result = [];
        foreach ($rawResult as $oidKey => $value) {
            $oids            = explode('.', $oidKey);
            $result[$oidKey] = $this->parseSnmpValue($value);
        }

        return $result;
    }
}
