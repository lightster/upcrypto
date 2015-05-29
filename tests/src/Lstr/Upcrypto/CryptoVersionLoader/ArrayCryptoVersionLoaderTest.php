<?php

namespace Lstr\Upcrypto\CryptoVersionLoader;

use Lstr\Upcrypto\CryptoVersionLoader\ArrayCryptoVersionLoader;
use PHPUnit_Framework_TestCase;

class ArrayCryptoVersionLoaderTest extends PHPUnit_Framework_TestCase
{
    public function testLatestCryptoVersionNumberIsCorrect()
    {
        $version = [
            'crypto_adapter' => '\Lstr\Upcrypto\CryptoAdapter\DefusePhpEncryption',
            'crypto_key'     => '\x1...',
        ];

        $version_loader = new ArrayCryptoVersionLoader([
            $version,
        ]);
        $this->assertEquals(1, $version_loader->getLatestCryptoVersionNumber());

        $version_loader = new ArrayCryptoVersionLoader([
            $version,
            $version,
        ]);
        $this->assertEquals(2, $version_loader->getLatestCryptoVersionNumber());

        $version_loader = new ArrayCryptoVersionLoader([
            $version,
            $version,
            $version,
        ]);
        $this->assertEquals(3, $version_loader->getLatestCryptoVersionNumber());
    }

    public function testLatestCryptoIsCorrectlyReturned()
    {
        $mock_builder = $this->getMockBuilder(
            '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
        );
        $adapters = [
            $mock_builder->getMock(),
            $mock_builder->getMock(),
            $mock_builder->getMock(),
        ];

        $versions = [
            [
                'crypto_adapter' => get_class($adapters[0]),
                'crypto_key'     => '\x1...',
            ],
            [
                'crypto_adapter' => get_class($adapters[1]),
                'crypto_key'     => '\x2...',
            ],
            [
                'crypto_adapter' => get_class($adapters[2]),
                'crypto_key'     => '\x3...',
            ],
        ];

        $version_loader = new ArrayCryptoVersionLoader([
            $versions[0],
        ]);
        $this->assertEquals(
            get_class($adapters[0]),
            get_class($version_loader->getLatestCrypto())
        );

        $version_loader = new ArrayCryptoVersionLoader([
            $versions[0],
            $versions[1],
        ]);
        $this->assertEquals(
            get_class($adapters[1]),
            get_class($version_loader->getLatestCrypto())
        );

        $version_loader = new ArrayCryptoVersionLoader([
            $versions[0],
            $versions[1],
            $versions[2],
        ]);
        $this->assertEquals(
            get_class($adapters[2]),
            get_class($version_loader->getLatestCrypto())
        );
    }

    public function testCryptoForVersionIsCorrectlyReturned()
    {
        $mock_builder = $this->getMockBuilder(
            '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
        );
        $adapters = [
            $mock_builder->getMock(),
            $mock_builder->getMock(),
            $mock_builder->getMock(),
        ];

        $versions = [
            [
                'crypto_adapter' => get_class($adapters[0]),
                'crypto_key'     => '\x1...',
            ],
            [
                'crypto_adapter' => get_class($adapters[1]),
                'crypto_key'     => '\x2...',
            ],
            [
                'crypto_adapter' => get_class($adapters[2]),
                'crypto_key'     => '\x3...',
            ],
        ];

        $version_loader = new ArrayCryptoVersionLoader([
            $versions[0],
            $versions[1],
            $versions[2],
        ]);
        $this->assertEquals(
            get_class($adapters[2]),
            get_class($version_loader->getCryptoForVersion(3))
        );
        $this->assertEquals(
            get_class($adapters[0]),
            get_class($version_loader->getCryptoForVersion(1))
        );
        $this->assertEquals(
            get_class($adapters[1]),
            get_class($version_loader->getCryptoForVersion(2))
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testCryptoForUnknownVersionThrowsAnException()
    {
        $mock_builder = $this->getMockBuilder(
            '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
        );
        $adapters = [
            $mock_builder->getMock(),
        ];

        $versions = [
            [
                'crypto_adapter' => get_class($adapters[0]),
                'crypto_key'     => '\x1...',
            ],
        ];

        $version_loader = new ArrayCryptoVersionLoader([
            $versions[0],
        ]);
        $version_loader->getCryptoForVersion(2);
    }

    public function testCryptoForVersionIsReused()
    {
        $mock_builder = $this->getMockBuilder(
            '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
        );
        $adapters = [
            $mock_builder->getMock(),
        ];

        $versions = [
            [
                'crypto_adapter' => get_class($adapters[0]),
                'crypto_key'     => '\x1...',
            ],
        ];

        $version_loader = new ArrayCryptoVersionLoader([
            $versions[0],
        ]);
        $this->assertSame(
            $version_loader->getCryptoForVersion(1),
            $version_loader->getCryptoForVersion(1)
        );
    }
}
