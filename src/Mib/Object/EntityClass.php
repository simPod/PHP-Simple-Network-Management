<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib\Object;

use Consistence\Enum\Enum;

class EntityClass extends Enum
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

    public static function getOther() : self
    {
        return self::get(self::OTHER);
    }

    public static function getUnknown() : self
    {
        return self::get(self::UNKNOWN);
    }

    public static function getChassis() : self
    {
        return self::get(self::CHASSIS);
    }

    public static function getBackplane() : self
    {
        return self::get(self::BACKPLANE);
    }

    public static function getContainer() : self
    {
        return self::get(self::CONTAINER);
    }

    public static function getPowerSupply() : self
    {
        return self::get(self::POWER_SUPPLY);
    }

    public static function getFan() : self
    {
        return self::get(self::FAN);
    }

    public static function getSensor() : self
    {
        return self::get(self::SENSOR);
    }

    public static function getModule() : self
    {
        return self::get(self::MODULE);
    }

    public static function getPort() : self
    {
        return self::get(self::PORT);
    }

    public static function getStack() : self
    {
        return self::get(self::STACK);
    }
}
