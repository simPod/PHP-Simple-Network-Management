<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib;

final class Iface
{
    public const OID_ADMIN_STATUS             = '.1.3.6.1.2.1.2.2.1.7';
    public const OID_ALIAS                    = '.1.3.6.1.2.1.31.1.1.1.18';
    public const OID_DESCRIPTION              = '.1.3.6.1.2.1.2.2.1.2';
    public const OID_NAME                     = '.1.3.6.1.2.1.31.1.1.1.1';
    public const OID_OPER_STATUS              = '.1.3.6.1.2.1.2.2.1.8';
    public const OID_SPEED                    = '.1.3.6.1.2.1.2.2.1.5';
    public const OID_HC_SPEED                 = '.1.3.6.1.2.1.31.1.1.1.15';
    public const OID_TYPE                     = '.1.3.6.1.2.1.2.2.1.3';
    public const OID_HC_IN_OCTETS             = '.1.3.6.1.2.1.31.1.1.1.6';
    public const OID_HC_OUT_OCTETS            = '.1.3.6.1.2.1.31.1.1.1.10';
    public const OID_HC_IN_BROADCAST_PACKETS  = '.1.3.6.1.2.1.31.1.1.1.9';
    public const OID_HC_OUT_BROADCAST_PACKETS = '.1.3.6.1.2.1.31.1.1.1.13';
    public const OID_HC_IN_MULTICAST_PACKETS  = '.1.3.6.1.2.1.31.1.1.1.8';
    public const OID_HC_OUT_MULTICAST_PACKETS = '.1.3.6.1.2.1.31.1.1.1.12';
    public const OID_HC_IN_UNICAST_PACKETS    = '.1.3.6.1.2.1.31.1.1.1.7';
    public const OID_HC_OUT_UNICAST_PACKETS   = '.1.3.6.1.2.1.31.1.1.1.11';
    public const OID_IN_ERRORS                = '.1.3.6.1.2.1.2.2.1.14';
    public const OID_OUT_ERRORS               = '.1.3.6.1.2.1.2.2.1.20';
    public const OID_IN_DISCARDS              = '.1.3.6.1.2.1.2.2.1.13';
    public const OID_OUT_DISCARDS             = '.1.3.6.1.2.1.2.2.1.19';
    public const OID_STACK_STATUS             = '.1.3.6.1.2.1.31.1.2.1.3';
}
