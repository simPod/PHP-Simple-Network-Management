<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib\Cisco;

/**
 * See CISCO-ENTITY-SENSOR-MIB
 */
final class EntitySensor
{
    public const OID_PHYSICAL_SENSOR_TYPE              = '.1.3.6.1.4.1.9.9.91.1.1.1.1.1';
    public const OID_PHYSICAL_SENSOR_SCALE             = '.1.3.6.1.4.1.9.9.91.1.1.1.1.2';
    public const OID_PHYSICAL_SENSOR_PRECISION         = '.1.3.6.1.4.1.9.9.91.1.1.1.1.3';
    public const OID_PHYSICAL_SENSOR_VALUE             = '.1.3.6.1.4.1.9.9.91.1.1.1.1.4';
    public const OID_PHYSICAL_SENSOR_OPER_STATUS       = '.1.3.6.1.4.1.9.9.91.1.1.1.1.5';
    public const OID_PHYSICAL_SENSOR_UNITS_DISPLAY     = '.1.3.6.1.4.1.9.9.91.1.1.1.1.6';
    public const OID_PHYSICAL_SENSOR_VALUE_TIME_STAMP  = '.1.3.6.1.4.1.9.9.91.1.1.1.1.7';
    public const OID_PHYSICAL_SENSOR_VALUE_UPDATE_RATE = '.1.3.6.1.4.1.9.9.91.1.1.1.1.8';
}
