<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib\Coriant;

/**
 * See Groove G30 release 2.1.0 https://mibs.observium.org/mib/CORIANT-GROOVE-MIB/
 */
final class Groove
{
    public const OID_SYSTEM_POWER_CONSUMPTION_CURRENT = '.1.3.6.1.4.1.42229.1.2.2.2.2';
    public const OID_CARD_ADMIN_STATUS                = '.1.3.6.1.4.1.42229.1.2.3.3.1.1.3';
    public const OID_CARD_OPER_STATUS                 = '.1.3.6.1.4.1.42229.1.2.3.3.1.1.4';
    public const OID_CARD_FAN_SPEED_RATE              = '.1.3.6.1.4.1.42229.1.2.3.3.1.1.7';
    public const OID_CARD_TEMPERATURE                 = '.1.3.6.1.4.1.42229.1.2.3.3.1.1.9';
    public const OID_SUBCARD_EQUIPMENT_NAME           = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.2';
    public const OID_PORT_RX_OPTICAL_POWER            = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.4';
    public const OID_PORT_TX_OPTICAL_POWER            = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.5';
    public const OID_PORT_RX_OPTICAL_POWER_LANE_1     = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.7';
    public const OID_PORT_RX_OPTICAL_POWER_LANE_2     = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.8';
    public const OID_PORT_RX_OPTICAL_POWER_LANE_3     = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.9';
    public const OID_PORT_RX_OPTICAL_POWER_LANE_4     = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.10';
    public const OID_PORT_TX_OPTICAL_POWER_LANE_1     = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.11';
    public const OID_PORT_TX_OPTICAL_POWER_LANE_2     = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.12';
    public const OID_PORT_TX_OPTICAL_POWER_LANE_3     = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.13';
    public const OID_PORT_TX_OPTICAL_POWER_LANE_4     = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.14';
    public const OID_PORT_NAME                        = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.16';
    public const OID_PORT_ADMIN_STATUS                = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.19';
    public const OID_PORT_OPER_STATUS                 = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.20';
    public const OID_PORT_ALIAS_NAME                  = '.1.3.6.1.4.1.42229.1.2.3.6.1.1.22';
    public const OID_INVENTORY_EQUIPMENT_TYPE         = '.1.3.6.1.4.1.42229.1.2.3.12.1.1.1';
    public const OID_INVENTORY_MODULE_TYPE            = '.1.3.6.1.4.1.42229.1.2.3.12.1.1.7';
    public const OID_INVENTORY_VENDOR                 = '.1.3.6.1.4.1.42229.1.2.3.12.1.1.8';
    public const OID_INVENTORY_SERIAL_NUMBER          = '.1.3.6.1.4.1.42229.1.2.3.12.1.1.9';
    public const OID_INVENTORY_FW_VERSION             = '.1.3.6.1.4.1.42229.1.2.3.12.1.1.10';
    public const OID_INVENTORY_PART_VERSION           = '.1.3.6.1.4.1.42229.1.2.3.12.1.1.11';
}
