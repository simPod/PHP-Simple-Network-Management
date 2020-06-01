<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib\Arista;

final class EntitySensor
{
    public const OID_THRESHOLD_LOW_WARNING   = '.1.3.6.1.4.1.30065.3.12.1.1.1.1';
    public const OID_THRESHOLD_LOW_CRITICAL  = '.1.3.6.1.4.1.30065.3.12.1.1.1.2';
    public const OID_THRESHOLD_HIGH_WARNING  = '.1.3.6.1.4.1.30065.3.12.1.1.1.3';
    public const OID_THRESHOLD_HIGH_CRITICAL = '.1.3.6.1.4.1.30065.3.12.1.1.1.4';
}
