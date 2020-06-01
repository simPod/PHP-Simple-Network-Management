<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib\Object;

final class EntityClass
{
    public const OTHER        = 1;
    public const UNKNOWN      = 2;
    public const CHASSIS      = 3;
    public const BACKPLANE    = 4;
    public const CONTAINER    = 5;
    public const POWER_SUPPLY = 6;
    public const FAN          = 7;
    public const SENSOR       = 8;
    public const MODULE       = 9;
    public const PORT         = 10;
    public const STACK        = 11;
}
