<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib\Dcp;

/**
 * See iso(1).org(3).dod(6).internet(1).private(4).enterprise(1).smartoptics(30826).dcp(2).dcpGeneric(2).dcpInterface(1)
 */
final class DcpInterface
{
    public const OID_DCP_INTERFACE_INDEX      = '.1.3.6.1.4.1.30826.2.2.1.1.1.1.1';
    public const OID_DCP_INTERFACE_NAME       = '.1.3.6.1.4.1.30826.2.2.1.1.1.1.2';
    public const OID_DCP_INTERFACE_RX_POWER   = '.1.3.6.1.4.1.30826.2.2.1.1.1.1.3';
    public const OID_DCP_INTERFACE_TX_POWER   = '.1.3.6.1.4.1.30826.2.2.1.1.1.1.4';
    public const OID_DCP_INTERFACE_STATUS     = '.1.3.6.1.4.1.30826.2.2.1.1.1.1.5';
    public const OID_DCP_INTERFACE_ALARM      = '.1.3.6.1.4.1.30826.2.2.1.1.1.1.6';
    public const OID_DCP_INTERFACE_FORMAT     = '.1.3.6.1.4.1.30826.2.2.1.1.1.1.7';
    public const OID_DCP_INTERFACE_WAVELENGTH = '.1.3.6.1.4.1.30826.2.2.1.1.1.1.8';
    public const OID_DCP_INTERFACE_CHANNEL_ID = '.1.3.6.1.4.1.30826.2.2.1.1.1.1.9';
}
