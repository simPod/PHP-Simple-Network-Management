<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use SimPod\PhpSnmp\Exception\GeneralException;
use SimPod\PhpSnmp\Helpers\OidStripper;
use SimPod\PhpSnmp\Transport\SnmpClient;

final class OidsStripperTest extends TestCase
{
    public function testStripParent() : void
    {
        $raw = [
            '.1.2.3.1' => 'a',
            '.1.2.3.2' => 'b',
            '.1.2.3.3' => 'c',
        ];

        $expected = [
            1 => 'a',
            2 => 'b',
            3 => 'c',
        ];

        self::assertSame($expected, OidStripper::stripParent($raw));
    }

    public function testStripParentEmptyData() : void
    {
        $this->expectExceptionObject(GeneralException::new('Expected non-empty array'));

        OidStripper::stripParent([]);
    }

    public function testStripParentInvalidKeys() : void
    {
        $this->expectExceptionObject(GeneralException::new('Expected keys to be full OIDs'));

        OidStripper::stripParent(['something strange' => 123]);
    }

    public function testWalk() : void
    {
        $raw = [
            '.1.2.3.1' => 'a',
            '.1.2.3.2' => 'b',
            '.1.2.3.3' => 'c',
        ];

        $expected = [
            '3.1' => 'a',
            '3.2' => 'b',
            '3.3' => 'c',
        ];

        $snmpClient = $this->createMock(SnmpClient::class);
        $snmpClient->expects(self::once())->method('walk')->with($oid = '.1.2')->willReturn($raw);

        self::assertSame($expected, OidStripper::walk($snmpClient, $oid));
    }
}
