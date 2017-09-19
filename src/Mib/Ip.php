<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Mib;

class Ip extends MibBase
{
    public const OID_IP_ADDRESS                   = '1.3.6.1.2.1.4.20.1.1';
    public const OID_IP_NET_TO_MEDIA_PHYS_ADDRESS = '1.3.6.1.2.1.4.22.1.2';

    /**
     * @return string[]
     */
    public function getIpAddress() : array
    {
        return $this->getSnmp()->walkFirstDegree(self::OID_IP_ADDRESS);
    }

    /**
     * @return string[]
     */
    public function getIpNetToMediaPhysAddress() : array
    {
        return $this->getSnmp()->walk(self::OID_IP_NET_TO_MEDIA_PHYS_ADDRESS);
    }
}
