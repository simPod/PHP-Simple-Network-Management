<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib\Object;

class HrDeviceStatus
{
    public const UNKNOWN = 1;
    public const RUNNING = 2;
    public const WARNING = 3;
    public const TESTING = 4;
    public const DOWN = 5;
}
