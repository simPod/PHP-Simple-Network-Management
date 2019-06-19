<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Transport;

use GuzzleHttp\Client;
use SimPod\PhpSnmp\Exception\SnmpApiError;
use function array_key_exists;
use function base64_decode;
use function is_array;
use function is_bool;
use function json_decode;
use function json_last_error_msg;
use function strtr;
use const JSON_BIGINT_AS_STRING;
use function var_dump;

final class ApiSnmp implements Snmp
{
    private const API_PATH = '/snmp/{host}/{community}/{oid}';

    /** @var Client */
    private $guzzle;

    /** @var string */
    private $apiHostUrl;

    /** @var string */
    private $community;

    /** @var string */
    private $host;

    /** @var int */
    private $timeout;

    public function __construct(
        Client $guzzle,
        string $apiHostUrl,
        string $community = 'public',
        string $host = '127.0.0.1',
        int $timeout = 30
    ) {
        $this->guzzle = $guzzle;
        $this->apiHostUrl = $apiHostUrl;
        $this->community  = $community;
        $this->host       = $host;
        $this->timeout    = $timeout;
    }

    /**
     * @return iterable<string, mixed>
     */
    public function walk(string $oid) : iterable
    {
        yield from $this->getResponse($oid, true);
    }

    /**
     * @return iterable<string, mixed>
     */
    public function walkWithCompleteOids(string $oid) : iterable
    {
        yield from $this->getResponse($oid, false);
    }

    /**
     * @return iterable<string, mixed>
     */
    private function getResponse(string $oid, bool $stripOidPrefix) : iterable
    {
        $url = $this->apiHostUrl . strtr(
            self::API_PATH,
            [
                '{community}' => $this->community,
                '{host}' => $this->host,
                '{oid}' => $oid,
            ]
        );

        if ($stripOidPrefix) {
            $url .= '?strip=1';
        }

        $response = $this->guzzle->get($url, ['timeout' => $this->timeout]);
        $json = json_decode((string) $response->getBody(), true, 4, JSON_BIGINT_AS_STRING);
        if ($json === null) {
            throw SnmpApiError::invalidJson(json_last_error_msg());
        }

        if (array_key_exists('error', $json)) {
            throw SnmpApiError::failed($json['error']);
        }

        if (! array_key_exists('response', $json)) {
            return [];
        }

        foreach ($json['response'] as $key => $value) {
            if (is_array($value) && $value['type'] === 'string') {
                $data = base64_decode($value['value'], true);
                if (is_bool($data)) { // https://github.com/phpstan/phpstan/pull/1366
                    throw SnmpApiError::invalidBase64String($value['value']);
                }

                yield $key => $data;
                continue;
            }

            yield $key => $value;
        }
    }
}
