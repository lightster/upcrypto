<?php

namespace Lstr\Upcrypto\CryptoVersionLoader;

use Lstr\Upcrypto\CryptoVersionLoader\ArrayCryptoVersionLoader;
use PHPUnit_Framework_TestCase;

class ArrayCryptoVersionLoaderTest extends PHPUnit_Framework_TestCase
{
    public function testLatestCryptoVersionNumberIsCorrect()
    {
        $version = [
            'crypto_adapter' => '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface',
        ];

        $version_loader = new ArrayCryptoVersionLoader([
            'a' => $version,
        ]);
        $this->assertEquals('a', $version_loader->getLatestCryptoVersionId());

        $version_loader = new ArrayCryptoVersionLoader([
            'a' => $version,
            'b' => $version,
        ]);
        $this->assertEquals('b', $version_loader->getLatestCryptoVersionId());

        $version_loader = new ArrayCryptoVersionLoader([
            'a' => $version,
            'b' => $version,
            'c' => $version,
        ]);
        $this->assertEquals('c', $version_loader->getLatestCryptoVersionId());
    }

    public function testLatestCryptoIsCorrectlyReturned()
    {
        $mock_builder = $this->getMockBuilder(
            '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
        );
        $adapters = [
            'a' => $mock_builder->getMock(),
            'b' => $mock_builder->getMock(),
            'c' => $mock_builder->getMock(),
        ];

        $versions = $this->getVersionsFromAdapters($adapters);

        $version_loader = new ArrayCryptoVersionLoader([
            'a' => $versions['a'],
        ]);
        $this->assertEquals(
            get_class($adapters['a']),
            get_class($version_loader->getLatestCrypto())
        );

        $version_loader = new ArrayCryptoVersionLoader([
            'a' => $versions['a'],
            'b' => $versions['b'],
        ]);
        $this->assertEquals(
            get_class($adapters['b']),
            get_class($version_loader->getLatestCrypto())
        );

        $version_loader = new ArrayCryptoVersionLoader([
            'a' => $versions['a'],
            'b' => $versions['b'],
            'c' => $versions['c'],
        ]);
        $this->assertEquals(
            get_class($adapters['c']),
            get_class($version_loader->getLatestCrypto())
        );
    }

    public function testCryptoForVersionIsCorrectlyReturned()
    {
        $mock_builder = $this->getMockBuilder(
            '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
        );
        $adapters = [
            'a' => $mock_builder->getMock(),
            'b' => $mock_builder->getMock(),
            'c' => $mock_builder->getMock(),
        ];

        $versions = $this->getVersionsFromAdapters($adapters);

        $version_loader = new ArrayCryptoVersionLoader([
            'a' => $versions['a'],
            'b' => $versions['b'],
            'c' => $versions['c'],
        ]);
        $this->assertEquals(
            get_class($adapters['c']),
            get_class($version_loader->getCryptoForVersion('c'))
        );
        $this->assertEquals(
            get_class($adapters['a']),
            get_class($version_loader->getCryptoForVersion('a'))
        );
        $this->assertEquals(
            get_class($adapters['b']),
            get_class($version_loader->getCryptoForVersion('b'))
        );
    }

    /**
     * @expectedException \Lstr\Upcrypto\Exception
     */
    public function testCryptoForUnknownVersionThrowsAnException()
    {
        $mock_builder = $this->getMockBuilder(
            '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
        );
        $adapters = [
            'a' => $mock_builder->getMock(),
        ];

        $versions = $this->getVersionsFromAdapters($adapters);

        $version_loader = new ArrayCryptoVersionLoader([
            $versions['a'],
        ]);
        $version_loader->getCryptoForVersion('c');
    }

    public function testCryptoForVersionIsReused()
    {
        $mock_builder = $this->getMockBuilder(
            '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
        );
        $adapters = [
            'a' => $mock_builder->getMock(),
        ];

        $versions = $this->getVersionsFromAdapters($adapters);

        $version_loader = new ArrayCryptoVersionLoader([
            'a' => $versions['a'],
        ]);
        $this->assertSame(
            $version_loader->getCryptoForVersion('a'),
            $version_loader->getCryptoForVersion('a')
        );
    }

    private function getVersionsFromAdapters(array $adapters)
    {
        $versions = [];

        foreach ($adapters as $id => $adapter) {
            $versions[$id] = [
                'crypto_adapter' => $adapter,
            ];
        }

        return $versions;
    }
}
