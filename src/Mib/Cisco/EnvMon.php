<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib\Cisco;

/**
 * See CISCO-ENVMON-MIB
 */
final class EnvMon
{
    public const OID_CISCO_ENV_MON_FAN_STATUS_DESRC   = '.1.3.6.1.4.1.9.9.13.1.4.1.2';
    public const OID_CISCO_ENV_MON_FAN_STATE          = '.1.3.6.1.4.1.9.9.13.1.4.1.3';
    public const OID_CISCO_ENV_MON_SUPPLY_STATUS_DESC = '.1.3.6.1.4.1.9.9.13.1.5.1.2';
    public const OID_CISCO_ENV_MON_SUPPLY_STATE       = '.1.3.6.1.4.1.9.9.13.1.5.1.3';
}
