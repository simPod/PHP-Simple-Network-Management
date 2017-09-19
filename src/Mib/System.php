<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib;

final class System extends MibBase
{
    public const OID_DESCRIPTION = '.1.3.6.1.2.1.1.1';
    public const OID_NAME        = '.1.3.6.1.2.1.1.5.0';
    public const OID_LOCATION    = '.1.3.6.1.2.1.1.6.0';
    public const OID_UPTIME      = '.1.3.6.1.2.1.1.3.0';

    /**
     * @return string[]
     */
    public function getDescription() : array
    {
        return $this->getSnmp()->walkFirstDegree(self::OID_DESCRIPTION);
    }

    /**
     * @return string[]
     */
    public function getLocation() : array
    {
        return $this->getSnmp()->walkFirstDegree(self::OID_LOCATION);
    }

    /**
     * @return string[]
     */
    public function getName() : array
    {
        return $this->getSnmp()->walkFirstDegree(self::OID_NAME);
    }

    /**
     * @return int[]
     */
    public function getUptime() : array
    {
        return $this->getSnmp()->walkFirstDegree(self::OID_UPTIME);
    }
}
