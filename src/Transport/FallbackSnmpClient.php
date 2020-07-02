<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Transport;

use Psr\Log\LoggerInterface;
use SimPod\PhpSnmp\Exception\GeneralException;

final class FallbackSnmpClient implements SnmpClient
{
    /** @var LoggerInterface */
    private $logger;

    /** @var SnmpClient[] */
    private $snmpClients;

    public function __construct(LoggerInterface $logger, SnmpClient ...$snmpClients)
    {
        if ($snmpClients === []) {
            throw GeneralException::new('No SNMP clients provided');
        }

        $this->logger      = $logger;
        $this->snmpClients = $snmpClients;
    }

    /** @inheritDoc */
    public function get(array $oids) : array
    {
        return $this->tryClients(
            static function (SnmpClient $client) use ($oids) : array {
                return $client->get($oids);
            }
        );
    }

    /** @inheritDoc */
    public function getNext(array $oids) : array
    {
        return $this->tryClients(
            static function (SnmpClient $client) use ($oids) : array {
                return $client->getNext($oids);
            }
        );
    }

    /** @inheritDoc */
    public function walk(string $oid, int $maxRepetitions = 40) : array
    {
        return $this->tryClients(
            static function (SnmpClient $client) use ($oid, $maxRepetitions) : array {
                return $client->walk($oid, $maxRepetitions);
            }
        );
    }

    /**
     * @param callable(SnmpClient): array<string, mixed> $requestCallback
     *
     * @return array<string, mixed>
     */
    private function tryClients(callable $requestCallback) : array
    {
        foreach ($this->snmpClients as $i => $snmpClient) {
            try {
                return $requestCallback($snmpClient);
            } catch (GeneralException $exception) {
                $this->logger->warning(
                    'SNMP request failed',
                    [
                        'sequenceNumber' => $i,
                        'client' => $snmpClient,
                        'exception' => $exception,
                    ]
                );
            }
        }

        /** @phpstan-ignore-next-line $exception will always be there */
        throw GeneralException::new('All SNMP clients failed, last error: ' . $exception->getMessage(), $exception);
    }
}
