<?php

namespace Lstr\Upcrypto;

use Lstr\Upcrypto\CryptoVersionLoader\ArrayCryptoVersionLoader;
use PHPUnit_Framework_TestCase;

class UpcryptoTest extends PHPUnit_Framework_TestCase
{
    public function testEncryptingAndDecryptingAStringReturnsOriginalString()
    {
        $test_text = 'something to be encrypted';
        $versions = [
            [
                'CryptoAdapter' => '\Lstr\Upcrypto\CryptoAdapter\DefusePhpEncryption',
                'CryptoKey' => '\x1...',
            ],
        ];

        $version_loader = new ArrayCryptoVersionLoader([
            $versions[0],
        ]);
        $upcrypto = new Upcrypto($version_loader);

        $this->assertEquals(
            $test_text,
            $upcrypto->decrypt($upcrypto->encrypt($test_text))
        );
    }

    public function testUpToDateEncryptionCanBeDetected()
    {
        $test_text = 'something recently encrypted';
        $versions = [
            [
                'CryptoAdapter' => '\Lstr\Upcrypto\CryptoAdapter\DefusePhpEncryption',
                'CryptoKey' => '\x1...',
            ],
            [
                'CryptoAdapter' => '\Lstr\Upcrypto\CryptoAdapter\DefusePhpEncryption',
                'CryptoKey' => '\x2...',
            ],
        ];

        $version_loader = new ArrayCryptoVersionLoader([
            $versions[0],
            $versions[1],
        ]);
        $upcrypto = new Upcrypto($version_loader);
        $encrypted = $upcrypto->encrypt(
            $test_text
        );

        $this->assertTrue(
            $upcrypto->isUpToDate($encrypted)
        );
    }

    public function testOutOfDateEncryptionCanBeDetected()
    {
        $test_text = 'something historically encrypted';
        $versions = [
            [
                'CryptoAdapter' => '\Lstr\Upcrypto\CryptoAdapter\DefusePhpEncryption',
                'CryptoKey' => '\x1...',
            ],
            [
                'CryptoAdapter' => '\Lstr\Upcrypto\CryptoAdapter\DefusePhpEncryption',
                'CryptoKey' => '\x2...',
            ],
        ];

        $historical_version_loader = new ArrayCryptoVersionLoader([
            $versions[0],
        ]);
        $historical_upcrypto = new Upcrypto($historical_version_loader);
        $historically_encrypted = $historical_upcrypto->encrypt(
            $test_text
        );

        $new_version_loader = new ArrayCryptoVersionLoader([
            $versions[0],
            $versions[1],
        ]);
        $new_upcrypto = new Upcrypto($new_version_loader);

        $this->assertFalse(
            $new_upcrypto->isUpToDate($historically_encrypted)
        );
    }

    public function testEncryptionCanBeUpgraded()
    {
        $test_text = 'something historically encrypted';
        $versions = [
            [
                'CryptoAdapter' => '\Lstr\Upcrypto\CryptoAdapter\DefusePhpEncryption',
                'CryptoKey' => '\x1...',
            ],
            [
                'CryptoAdapter' => '\Lstr\Upcrypto\CryptoAdapter\DefusePhpEncryption',
                'CryptoKey' => '\x2...',
            ],
        ];

        $historical_version_loader = new ArrayCryptoVersionLoader([
            $versions[0],
        ]);
        $historical_upcrypto = new Upcrypto($historical_version_loader);
        $historically_encrypted = $historical_upcrypto->encrypt(
            $test_text
        );

        $new_version_loader = new ArrayCryptoVersionLoader([
            $versions[0],
            $versions[1],
        ]);
        $new_upcrypto = new Upcrypto($new_version_loader);

        $this->assertFalse(
            $new_upcrypto->isUpToDate($historically_encrypted)
        );
        $encrypted_with_upgrade = $new_upcrypto->upgradeEncryption(
            $historically_encrypted
        );
        $this->assertTrue(
            $new_upcrypto->isUpToDate($encrypted_with_upgrade)
        );
        $this->assertEquals(
            $test_text,
            $new_upcrypto->decrypt($encrypted_with_upgrade)
        );
    }
}
