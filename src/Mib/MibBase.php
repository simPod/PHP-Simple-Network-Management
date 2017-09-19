<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib;

use SimPod\PhpSnmp\Snmp;

abstract class MibBase implements Mib
{
    public const OBJECT_TYPES = [
        HostResources::OID_HR_STORAGE_TYPES => null,
        HostResources::OID_HR_DEVICE_TYPES => null,
        HostResources::OID_HR_FSTYPES => null,
    ];

    /** @var Snmp */
    private $snmp;

    public function __construct(Snmp $snmp)
    {
        $this->snmp = $snmp;
    }

    public function getSnmp() : Snmp
    {
        return $this->snmp;
    }
}
