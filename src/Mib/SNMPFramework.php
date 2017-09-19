<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib;

final class SNMPFramework extends MibBase
{
    public const OID_SNMP_ENGINE_TIME = '.1.3.6.1.6.3.10.2.1.3';

    /**
     * @return int[]
     */
    public function getEngineTime() : array
    {
        return $this->getSnmp()->walkFirstDegree(self::OID_SNMP_ENGINE_TIME);
    }
}
