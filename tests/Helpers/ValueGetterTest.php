<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use SimPod\PhpSnmp\Exception\GeneralException;
use SimPod\PhpSnmp\Exception\NoSuchInstanceExists;
use SimPod\PhpSnmp\Helpers\ValueGetter;
use SimPod\PhpSnmp\Transport\SnmpClient;

final class ValueGetterTest extends TestCase
{
    public function testGetFirst() : void
    {
        $raw = ['.1.2.3.1' => $expected = 'a'];

        self::assertSame($expected, ValueGetter::first($raw));
    }

    public function testGetFirstWithUnexpectedData() : void
    {
        $this->expectExceptionObject(GeneralException::new('Expected non-empty array'));

        ValueGetter::first([]);
    }

    public function testGetFirstFromSameTree() : void
    {
        $snmpClient = $this->createMock(SnmpClient::class);
        $snmpClient->expects(self::once())
            ->method('getNext')
            ->willReturn(['.1.2.3.1' => $expected = 'a']);

        self::assertSame($expected, ValueGetter::firstFromSameTree($snmpClient, '.1.2.3'));
    }

    public function testGetFirstFromSameTreeDoesntExist() : void
    {
        $snmpClient = $this->createMock(SnmpClient::class);
        $snmpClient->expects(self::once())
            ->method('getNext')
            ->willReturn(['.1.2.4.1' => 'a']);

        $this->expectExceptionObject(NoSuchInstanceExists::fromOid('.1.2.3'));
        ValueGetter::firstFromSameTree($snmpClient, '.1.2.3');
    }

    public function testGetFirstFromSameTrees() : void
    {
        $snmpClient = $this->createMock(SnmpClient::class);
        $snmpClient->expects(self::once())
            ->method('getNext')
            ->willReturn(
                [
                    '.1.2.3.1' => 'a',
                    '.1.2.6.1' => 'b',
                ]
            );

        $expected = ['a', 'b'];

        self::assertSame($expected, ValueGetter::firstFromSameTrees($snmpClient, ['.1.2.3', '.1.2.6']));
    }

    public function testGetFirstFromSameTreesDoesntExist() : void
    {
        $snmpClient = $this->createMock(SnmpClient::class);
        $snmpClient->expects(self::once())
            ->method('getNext')
            ->willReturn(
                [
                    '.1.2.3.1' => 'a',
                    '.1.2.7.1' => 'b',
                ]
            );

        $this->expectExceptionObject(NoSuchInstanceExists::fromOid('.1.2.6'));
        ValueGetter::firstFromSameTrees($snmpClient, ['.1.2.3', '.1.2.6']);
    }
}
