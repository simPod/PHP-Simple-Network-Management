<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib\Dcp;

/**
 * See iso(1).org(3).dod(6).internet(1).private(4).enterprise(1).smartoptics(30826).dcp(2).dcpGeneric(2).dcpAlarm(1)
 */
final class DcpAlarm
{
    public const OID_DCP_ALARM_LOG_LIST_INDEX          = '.1.3.6.1.4.1.30826.2.2.2.2.2.1.1';
    public const OID_DCP_ALARM_LOG_LIST_LOCATION       = '.1.3.6.1.4.1.30826.2.2.2.2.2.1.2';
    public const OID_DCP_ALARM_LOG_LIST_INTERFACE_NAME = '.1.3.6.1.4.1.30826.2.2.2.2.2.1.3';
    public const OID_DCP_ALARM_LOG_LIST_TEXT           = '.1.3.6.1.4.1.30826.2.2.2.2.2.1.4';
    public const OID_DCP_ALARM_LOG_LIST_SEVERITY       = '.1.3.6.1.4.1.30826.2.2.2.2.2.1.5';
    public const OID_DCP_ALARM_LOG_LIST_START_TIME     = '.1.3.6.1.4.1.30826.2.2.2.2.2.1.6';
    public const OID_DCP_ALARM_LOG_LIST_END_TIME       = '.1.3.6.1.4.1.30826.2.2.2.2.2.1.7';
    public const OID_DCP_ALARM_LOG_LIST_SEQ_NUMBER     = '.1.3.6.1.4.1.30826.2.2.2.2.2.1.8';
}
