<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Transport;

use SimPod\PhpSnmp\Exception\EndOfMibReached;
use SimPod\PhpSnmp\Exception\GeneralException;
use SimPod\PhpSnmp\Exception\InvalidVersionProvided;
use SimPod\PhpSnmp\Exception\NoSuchInstanceExists;
use SimPod\PhpSnmp\Exception\NoSuchObjectExists;
use SimPod\PhpSnmp\Exception\SnmpException;
use SimPod\PhpSnmp\Transport\Cli\ProcessExecutor;
use SimPod\PhpSnmp\Transport\Cli\SymfonyProcessProcessExecutor;
use Throwable;
use function array_merge;
use function assert;
use function explode;
use function in_array;
use function Safe\preg_match;
use function Safe\substr;
use function strlen;
use function strpos;

final class CliSnmpClient implements SnmpClient
{
    /** @var ProcessExecutor */
    private $processExecutor;

    /** @var string[] */
    private $processArgs;

    /** @var bool */
    private $useBulk;

    public function __construct(
        string $host = '127.0.0.1',
        string $community = 'public',
        int $timeout = 1,
        int $retries = 3,
        string $version = '2c',
        ?ProcessExecutor $processExecutor = null
    ) {
        if (! in_array($version, ['1', '2c'], true)) {
            throw InvalidVersionProvided::new($version);
        }

        $this->processExecutor = $processExecutor ?? new SymfonyProcessProcessExecutor(120);
        $this->processArgs     = [
            '-ObenU',
            '--hexOutputLength=0',
            '-t',
            (string) $timeout,
            '-r',
            (string) $retries,
            '-v',
            $version,
            '-c',
            $community,
            $host,
        ];
        $this->useBulk         = $version === '2c';
    }

    /** @inheritDoc */
    public function get(array $oids) : array
    {
        try {
            $output = $this->processExecutor->execute(array_merge(['snmpget'], $this->processArgs, $oids));
        } catch (Throwable $throwable) {
            // check for SNMP v1
            if (preg_match('~\(noSuchName\).+Failed object: (.+?)$~ms', $throwable->getMessage(), $matches) === 1) {
                throw NoSuchInstanceExists::fromOid($matches[1]);
            }

            throw $this->processException($throwable, $oids);
        }

        return $this->processOutput($output);
    }

    /** @inheritDoc */
    public function getNext(array $oids) : array
    {
        try {
            $output = $this->processExecutor->execute(array_merge(['snmpgetnext'], $this->processArgs, $oids));
        } catch (Throwable $throwable) {
            // check for SNMP v1
            if (preg_match('~\(noSuchName\).+Failed object: (.+?)$~ms', $throwable->getMessage(), $matches) === 1) {
                throw EndOfMibReached::fromOid($matches[1]);
            }

            throw $this->processException($throwable, $oids);
        }

        return $this->processOutput($output);
    }

    /** @inheritDoc */
    public function walk(string $oid, int $maxRepetitions = 40) : array
    {
        $walker = 'snmpwalk';
        $args   = $this->processArgs;
        if ($this->useBulk) {
            $walker = 'snmpbulkwalk';
            $args   = array_merge($args, ['-Cr' . $maxRepetitions]);
        }

        try {
            $output = $this->processExecutor->execute(array_merge([$walker], $args, [$oid]));
        } catch (Throwable $throwable) {
            throw $this->processException($throwable, [$oid]);
        }

        $result = $this->processOutput($output);
        if ($result === []) {
            throw NoSuchInstanceExists::fromOid($oid);
        }

        return $result;
    }

    /** @return array<string, mixed> */
    private function processOutput(string $output) : array
    {
        $outputLength = strlen($output);
        $lineStartPos = 0;
        $result       = [];

        while ($lineStartPos < $outputLength) {
            $newLinePos = strpos($output, "\n", $lineStartPos);
            assert($newLinePos !== false);

            $quotesPos = strpos($output, '"', $lineStartPos);
            if ($quotesPos === false || $quotesPos > $newLinePos) {
                $lineEndPos = $this->getLineEndPos($output, $newLinePos, $lineStartPos);

                $result = $this->processOutputLine($result, substr($output, $lineStartPos, $lineEndPos));

                $lineStartPos = $newLinePos + 1;

                continue;
            }

            while (true) {
                $endQuotesPos = strpos($output, '"', $quotesPos + 1);
                assert($endQuotesPos !== false);

                if ($output[$endQuotesPos - 1] !== '\\' && $output[$endQuotesPos - 2] !== '\\') {
                    break;
                }

                $quotesPos = $endQuotesPos;
            }

            $newLinePos = strpos($output, "\n", $endQuotesPos);
            assert($newLinePos !== false);

            $lineEndPos = $this->getLineEndPos($output, $newLinePos, $lineStartPos);

            $result = $this->processOutputLine($result, substr($output, $lineStartPos, $lineEndPos));

            $lineStartPos = $newLinePos + 1;
        }

        return $result;
    }

    private function getLineEndPos(string $output, int $newLinePos, int $i) : int
    {
        if ($output[$newLinePos - 1] === "\r") {
            return $newLinePos - $i - 1;
        }

        return $newLinePos - $i;
    }

    /**
     * @param array<string, int|string> $result
     *
     * @return array<string, int|string>
     */
    private function processOutputLine(array $result, string $line) : array
    {
        // check for SNMP v1
        if (strpos($line, 'End of MIB') === 0) {
            if ($result !== []) {
                return $result;
            }

            throw EndOfMibReached::new();
        }

        [$oid, $value] = explode(' = ', $line, 2);

        if (strpos($value, 'No Such Object') === 0) {
            throw NoSuchObjectExists::fromOid($oid);
        }

        if (strpos($value, 'No Such Instance') === 0) {
            throw NoSuchInstanceExists::fromOid($oid);
        }

        if (strpos($value, 'No more variables left in this MIB View') === 0) {
            if ($result !== []) {
                return $result;
            }

            throw EndOfMibReached::fromOid($oid);
        }

        $result[$oid] = ValueParser::parse($value);

        return $result;
    }

    /** @param list<string> $oids */
    private function processException(Throwable $throwable, array $oids) : Throwable
    {
        if ($throwable instanceof SnmpException) {
            return GeneralException::fromThrowable($throwable, $oids);
        }

        return GeneralException::new('Failed to execute SNMP CLI command', $throwable, $oids);
    }
}
