<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Exception;

use RuntimeException;
use SimPod\PhpSnmp\SnmpException;

final class SnmpFailed extends RuntimeException implements SnmpException
{
}
