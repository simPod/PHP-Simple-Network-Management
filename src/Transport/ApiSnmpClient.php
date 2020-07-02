<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Transport;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Safe\Exceptions\JsonException;
use SimPod\PhpSnmp\Exception\EndOfMibReached;
use SimPod\PhpSnmp\Exception\GeneralException;
use SimPod\PhpSnmp\Exception\NoSuchInstanceExists;
use SimPod\PhpSnmp\Exception\NoSuchObjectExists;
use Throwable;
use function array_key_exists;
use function count;
use function Safe\json_decode;
use function Safe\json_encode;
use function Safe\preg_match;
use function Safe\sprintf;
use const JSON_BIGINT_AS_STRING;

final class ApiSnmpClient implements SnmpClient
{
    private const API_PATH = '/snmp-proxy';

    private const REQUEST_TYPE_GET      = 'get';
    private const REQUEST_TYPE_GET_NEXT = 'getNext';
    private const REQUEST_TYPE_WALK     = 'walk';

    /** @var ClientInterface */
    private $client;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    /** @var string */
    private $apiHostUrl;

    /** @var string */
    private $host;

    /** @var string */
    private $community;

    /** @var int */
    private $timeout;

    /** @var int */
    private $retries;

    /** @var string */
    private $version;

    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        string $apiHostUrl,
        string $host = '127.0.0.1',
        string $community = 'public',
        int $timeout = 1,
        int $retries = 3,
        string $version = '2c'
    ) {
        $this->client         = $client;
        $this->requestFactory = $requestFactory;
        $this->streamFactory  = $streamFactory;
        $this->apiHostUrl     = $apiHostUrl;
        $this->host           = $host;
        $this->community      = $community;
        $this->timeout        = $timeout;
        $this->retries        = $retries;
        $this->version        = $version;
    }

    /** @inheritDoc */
    public function get(array $oids) : array
    {
        return $this->executeRequest(self::REQUEST_TYPE_GET, $oids);
    }

    /** @inheritDoc */
    public function getNext(array $oids) : array
    {
        return $this->executeRequest(self::REQUEST_TYPE_GET_NEXT, $oids);
    }

    /** @inheritDoc */
    public function walk(string $oid, int $maxRepetitions = 40) : array
    {
        return $this->executeRequest(self::REQUEST_TYPE_WALK, [$oid], $maxRepetitions);
    }

    /**
     * @param string[] $oids
     *
     * @return array<string, mixed>
     */
    private function executeRequest(string $requestType, array $oids, ?int $maxRepetitions = null) : array
    {
        $requestParameters = [
            'request_type' => $requestType,
            'host' => $this->host,
            'community' => $this->community,
            'oids' => $oids,
            'version' => $this->version,
            'timeout' => $this->timeout,
            'retries' => $this->retries,
        ];

        if ($maxRepetitions !== null) {
            $requestParameters['max_repetitions'] = $maxRepetitions;
        }

        $request = $this->requestFactory->createRequest('POST', $this->apiHostUrl . self::API_PATH)
            ->withBody($this->streamFactory->createStream(json_encode($requestParameters)));

        try {
            $response = $this->client->sendRequest($request);
        } catch (Throwable $throwable) {
            throw GeneralException::fromThrowable($throwable, $oids);
        }

        try {
            /** @var array{error: string}|array{result: list<string>} $result */
            $result = json_decode((string) $response->getBody(), true, 4, JSON_BIGINT_AS_STRING);
        } catch (JsonException $throwable) {
            $error = sprintf(
                'Response is not valid JSON [HTTP %d]: "%s"',
                $response->getStatusCode(),
                (string) $response->getBody()
            );

            throw GeneralException::new($error, $throwable, $oids);
        }

        if (array_key_exists('error', $result)) {
            if (preg_match('~no such object: (.+)~', $result['error'], $matches) === 1) {
                throw NoSuchObjectExists::fromOid($matches[1]);
            }

            if (preg_match('~no such instance: (.+)~', $result['error'], $matches) === 1) {
                throw NoSuchInstanceExists::fromOid($matches[1]);
            }

            if (preg_match('~end of mib: (.+)~', $result['error'], $matches) === 1) {
                throw EndOfMibReached::fromOid($matches[1]);
            }

            throw GeneralException::new($result['error'], null, $oids);
        }

        $oidsAndValues = $result['result'];

        $result = [];
        for ($i = 0, $iMax = count($oidsAndValues); $i < $iMax; $i += 2) {
            $key   = $oidsAndValues[$i];
            $value = $oidsAndValues[$i + 1];

            $result[$key] = $value;
        }

        return $result;
    }
}
