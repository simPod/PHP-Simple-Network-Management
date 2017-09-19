<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib;

use SimPod\PhpSnmp\Snmp;

interface Mib
{
    public const NOSUCHINSTANCE_VALUE = 'NOSUCHINSTANCE';

    public function getSnmp() : Snmp;
}
