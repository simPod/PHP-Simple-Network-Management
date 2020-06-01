<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Tests\Transport;

use PHPUnit\Framework\TestCase;
use SimPod\PhpSnmp\Exception\GeneralException;
use SimPod\PhpSnmp\Transport\FallbackSnmpClient;
use SimPod\PhpSnmp\Transport\SnmpClient;

final class FallbackSnmpClientTest extends TestCase
{
    public function testGet() : void
    {
        $client1 = $this->createMock(SnmpClient::class);
        $client1->expects(self::once())
            ->method('get')
            ->with($oids = ['.1.2.3'])
            ->willReturn($expected = ['.1.2.3' => 123]);

        $fallbackClient = new FallbackSnmpClient($client1);
        $result         = $fallbackClient->get($oids);

        self::assertSame($expected, $result);
    }

    public function testGetNext() : void
    {
        $client1 = $this->createMock(SnmpClient::class);
        $client1->expects(self::once())
            ->method('getNext')
            ->with($oids = ['.1.2.3'])
            ->willReturn($expected = ['.1.2.3' => 123]);

        $fallbackClient = new FallbackSnmpClient($client1);
        $result         = $fallbackClient->getNext($oids);

        self::assertSame($expected, $result);
    }

    public function testWalk() : void
    {
        $client1 = $this->createMock(SnmpClient::class);
        $client1->expects(self::once())
            ->method('walk')
            ->with($oid = '.1.2.3')
            ->willReturn($expected = ['.1.2.3' => 123]);

        $fallbackClient = new FallbackSnmpClient($client1);
        $result         = $fallbackClient->walk($oid);

        self::assertSame($expected, $result);
    }

    public function testOnlyLastClientWorks() : void
    {
        $client1 = $this->createMock(SnmpClient::class);
        $client1->expects(self::once())
            ->method('get')
            ->with($oids = ['.1.2.3'])
            ->willThrowException(GeneralException::new('an error'));

        $client2 = $this->createMock(SnmpClient::class);
        $client2->expects(self::once())
            ->method('get')
            ->with($oids = ['.1.2.3'])
            ->willThrowException(GeneralException::new('other error'));

        $client3 = $this->createMock(SnmpClient::class);
        $client3->expects(self::once())
            ->method('get')
            ->with($oids = ['.1.2.3'])
            ->willReturn($expected = ['.1.2.3' => 123]);

        $fallbackClient = new FallbackSnmpClient($client1, $client2, $client3);
        $result         = $fallbackClient->get($oids);

        self::assertSame($expected, $result);
    }

    public function testAllClientsFail() : void
    {
        $client1 = $this->createMock(SnmpClient::class);
        $client1->expects(self::once())
            ->method('get')
            ->with($oids = ['.1.2.3'])
            ->willThrowException(GeneralException::new('an error'));

        $client2 = $this->createMock(SnmpClient::class);
        $client2->expects(self::once())
            ->method('get')
            ->with($oids = ['.1.2.3'])
            ->willThrowException($expected = GeneralException::new('other error'));

        $fallbackClient = new FallbackSnmpClient($client1, $client2);

        $this->expectExceptionObject($expected);

        $fallbackClient->get($oids);
    }

    public function testNoClientsProvided() : void
    {
        $this->expectExceptionObject(GeneralException::new('No SNMP clients provided'));

        new FallbackSnmpClient();
    }
}
