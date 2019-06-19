<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Tests\Helper;

use PHPStan\Testing\TestCase;
use SimPod\PhpSnmp\Helper\MacAddress;
use function iterable_to_array;
use function pack;

final class MacAddressTest extends TestCase
{
    /**
     * @dataProvider providerNormalize
     */
    public function testNormalize(string $macAddress, ?string $expected) : void
    {
        self::assertSame($expected, MacAddress::normalize($macAddress));
    }

    /**
     * @return iterable<array<string|null>>
     */
    public function providerNormalize() : iterable
    {
        yield [pack('C*', 120, 186, 249, 65, 240, 242), '78:ba:f9:41:f0:f2'];
        yield ['a8:c:d:19:a4:d0', 'a8:0c:0d:19:a4:d0'];
        yield ['a8:C:D:19:A4:D0', 'a8:0c:0d:19:a4:d0'];
        yield ['00:00:00:00:00:00', '00:00:00:00:00:00'];
        yield ['a8-c-d-19-a4-d0', 'a8:0c:0d:19:a4:d0'];
        yield ['a8 c d 19 a4 d0', 'a8:0c:0d:19:a4:d0'];
        yield ['a80c.0d19.a4d0', 'a8:0c:0d:19:a4:d0'];
        yield ['a80c.a.a4d0', 'a8:0c:00:0a:a4:d0'];

        yield [pack('C*', 120, 186, 249, 65, 240, 242, 255), null];
        yield [pack('C*', 120, 186, 249, 65, 240), null];
        yield ['abcdefg', null];
        yield ['a8:c:d:19:a4:g0', null];
        yield ['a8:c:19:a4:d0', null];
        yield ['a8-c-d-19-a4_d0', null];
        yield ['a8:c:d:19:a4:d0:d0', null];
        yield ['a8:c d-19.a4:d0', null];
    }

    /**
     * @param array<string, string> $macAddresses
     * @param array<string, string> $expectedMacAddresses
     *
     * @dataProvider providerNormalizeBulk
     */
    public function testNormalizeBulk(array $macAddresses, array $expectedMacAddresses) : void
    {
        self::assertSame($expectedMacAddresses, iterable_to_array(MacAddress::normalizeBulk($macAddresses)));
    }

    /**
     * @return iterable<array<array<int, string>>>
     */
    public function providerNormalizeBulk() : iterable
    {
        yield [
            [
                5 => pack('C*', 120, 186, 249, 65, 240, 242),
                15 => 'a8:c:d:19:a4:d0',
                25 => 'a8:c:19:a4:d0', // invalid
                132 => 'a8-c-d-19-a4-d0',
                29 => 'a8-c-d-19-a4_d0', // invalid
            ],
            [
                5 => '78:ba:f9:41:f0:f2',
                15 => 'a8:0c:0d:19:a4:d0',
                132 => 'a8:0c:0d:19:a4:d0',
            ],
        ];
    }
}
