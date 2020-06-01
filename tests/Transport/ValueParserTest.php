<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Tests\Transport;

use PHPUnit\Framework\TestCase;
use SimPod\PhpSnmp\Exception\CannotParseUnknownValueType;
use SimPod\PhpSnmp\Transport\ValueParser;

final class ValueParserTest extends TestCase
{
    /**
     * @param mixed $expected
     *
     * @dataProvider providerParse
     */
    public function testParse(string $raw, $expected) : void
    {
        self::assertSame($expected, ValueParser::parse($raw));
    }

    /** @return iterable<list<mixed>> */
    public function providerParse() : iterable
    {
        yield 'Counter64' => ['Counter64: 123456', '123456'];
        yield 'Hex-STRING' => ['Hex-STRING: A1 B2 C3', 'A1 B2 C3'];
        yield 'IpAddress' => ['IpAddress: 127.0.0.1', '127.0.0.1'];
        yield 'OID' => ['OID: .1.2.3', '.1.2.3'];
        yield 'STRING' => ['STRING: "abc"', 'abc'];
        yield 'empty string' => ['""', ''];
        yield 'INTEGER' => ['INTEGER: 123', 123];
        yield 'Counter32' => ['Counter32: 123', 123];
        yield 'Gauge32' => ['Gauge32: 123', 123];
        yield 'Timeticks' => ['Timeticks: (3171608622) 367 days, 2:01:26.22', 3171608622];
    }

    public function testParseWithUnknownValueType() : void
    {
        $this->expectExceptionObject(CannotParseUnknownValueType::new('Wow'));

        ValueParser::parse('Wow: lol');
    }
}
