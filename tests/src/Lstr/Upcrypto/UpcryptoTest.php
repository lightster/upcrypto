<?php

namespace Lstr\Upcrypto;

use PHPUnit_Framework_TestCase;

class UpcryptoTest extends PHPUnit_Framework_TestCase
{
    public function testEncryptingCallsEncryptOnTheCryptoAdapter()
    {
        $crypto_adapter = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
            )
            ->getMock();
        $crypto_adapter
            ->expects($this->once())
            ->method('encrypt')
            ->with($this->equalTo('unencrypted_string'));

        $version_loader = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoVersionLoader\AbstractCryptoVersionLoader'
            )
            ->setMethods(['getLatestCryptoVersionId', 'getCryptoForVersion'])
            ->getMock();
        $version_loader
            ->expects($this->any())
            ->method('getCryptoForVersion')
            ->will($this->returnValue($crypto_adapter));

        $upcrypto = new Upcrypto($version_loader);

        $upcrypto->encrypt('unencrypted_string');
    }

    public function testDecryptingCallsDecryptOnTheCryptoAdapter()
    {
        $crypto_adapter = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
            )
            ->getMock();
        $crypto_adapter
            ->expects($this->once())
            ->method('decrypt')
            ->will($this->returnValue('unencrypted_string'));

        $version_loader = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoVersionLoader\AbstractCryptoVersionLoader'
            )
            ->setMethods(['getLatestCryptoVersionId', 'getCryptoForVersion'])
            ->getMock();
        $version_loader
            ->expects($this->any())
            ->method('getCryptoForVersion')
            ->will($this->returnValue($crypto_adapter));

        $upcrypto = new Upcrypto($version_loader);

        $this->assertEquals(
            'unencrypted_string',
            $upcrypto->decrypt($upcrypto->encrypt('unencrypted_string'))
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testDecryptingInvalidEncryptionObjectThrowsAnException()
    {
        $version_loader = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoVersionLoader\AbstractCryptoVersionLoader'
            )
            ->getMock();

        $upcrypto = new Upcrypto($version_loader);

        $upcrypto->decrypt('something_invalid');
    }

    /**
     * @expectedException \Exception
     */
    public function testDecryptingEncryptionObjectWithoutACipherVersionThrowsAnException()
    {
        $version_loader = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoVersionLoader\AbstractCryptoVersionLoader'
            )
            ->getMock();

        $upcrypto = new Upcrypto($version_loader);

        $upcrypto->decrypt(json_encode(['cipher' => 'anything']));
    }

    /**
     * @expectedException \Exception
     */
    public function testDecryptingInvalidEncryptionObjectsThrowsAnException()
    {
        $version_loader = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoVersionLoader\AbstractCryptoVersionLoader'
            )
            ->getMock();

        $upcrypto = new Upcrypto($version_loader);

        $upcrypto->decrypt(json_encode(['crypto_version' => 5]));
    }

    public function testEncryptingAndDecryptingAStringReturnsOriginalString()
    {
        $crypto_adapter = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
            )
            ->getMock();
        $crypto_adapter
            ->expects($this->once())
            ->method('encrypt')
            ->with($this->equalTo('unencrypted_string'))
            ->will($this->returnValue('encrypted_string'));
        $crypto_adapter
            ->expects($this->once())
            ->method('decrypt')
            ->with($this->equalTo('encrypted_string'))
            ->will($this->returnValue('unencrypted_string'));

        $version_loader = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoVersionLoader\AbstractCryptoVersionLoader'
            )
            ->setMethods(['getLatestCryptoVersionId', 'getCryptoForVersion'])
            ->getMock();
        $version_loader
            ->expects($this->any())
            ->method('getCryptoForVersion')
            ->will($this->returnValue($crypto_adapter));

        $upcrypto = new Upcrypto($version_loader);

        $this->assertEquals(
            'unencrypted_string',
            $upcrypto->decrypt($upcrypto->encrypt('unencrypted_string'))
        );
    }

    public function testUpToDateEncryptionCanBeDetected()
    {
        $crypto_adapter = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
            )
            ->getMock();

        $version_loader = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoVersionLoader\AbstractCryptoVersionLoader'
            )
            ->setMethods(['getLatestCryptoVersionId', 'getCryptoForVersion'])
            ->getMock();
        $version_loader
            ->expects($this->any())
            ->method('getLatestCryptoVersionId')
            ->will($this->returnValue(2));
        $version_loader
            ->expects($this->once())
            ->method('getCryptoForVersion')
            ->will($this->returnValue($crypto_adapter));

        $upcrypto = new Upcrypto($version_loader);

        $this->assertTrue(
            $upcrypto->isUpToDate($upcrypto->encrypt('recently encrypted'))
        );
    }

    public function testOutOfDateEncryptionCanBeDetected()
    {
        $crypto_adapter = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
            )
            ->getMock();

        $historical_version_loader = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoVersionLoader\AbstractCryptoVersionLoader'
            )
            ->setMethods(['getLatestCryptoVersionId', 'getCryptoForVersion'])
            ->getMock();
        $historical_version_loader
            ->expects($this->any())
            ->method('getLatestCryptoVersionId')
            ->will($this->returnValue(1));
        $historical_version_loader
            ->expects($this->once())
            ->method('getCryptoForVersion')
            ->will($this->returnValue($crypto_adapter));

        $new_version_loader = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoVersionLoader\AbstractCryptoVersionLoader'
            )
            ->setMethods(['getLatestCryptoVersionId', 'getCryptoForVersion'])
            ->getMock();
        $new_version_loader
            ->expects($this->any())
            ->method('getLatestCryptoVersionId')
            ->will($this->returnValue(2));

        $historical_upcrypto = new Upcrypto($historical_version_loader);
        $new_upcrypto = new Upcrypto($new_version_loader);

        $this->assertFalse(
            $new_upcrypto->isUpToDate(
                $historical_upcrypto->encrypt('historically encrypted')
            )
        );
    }

    public function testEncryptionCanBeUpgraded()
    {
        $crypto_adapter = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
            )
            ->getMock();
        $crypto_adapter
            ->expects($this->once())
            ->method('decrypt');
        // encrypt is called once to encrypt the value and
        // then again to encrypt the value again
        $crypto_adapter
            ->expects($this->exactly(2))
            ->method('encrypt');

        $historical_version_loader = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoVersionLoader\AbstractCryptoVersionLoader'
            )
            ->setMethods(['getLatestCryptoVersionId', 'getCryptoForVersion'])
            ->getMock();
        $historical_version_loader
            ->expects($this->any())
            ->method('getLatestCryptoVersionId')
            ->will($this->returnValue(1));
        $historical_version_loader
            ->expects($this->any())
            ->method('getCryptoForVersion')
            ->will($this->returnValue($crypto_adapter));

        $new_version_loader = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoVersionLoader\AbstractCryptoVersionLoader'
            )
            ->setMethods(['getLatestCryptoVersionId', 'getCryptoForVersion'])
            ->getMock();
        $new_version_loader
            ->expects($this->any())
            ->method('getLatestCryptoVersionId')
            ->will($this->returnValue(2));
        $new_version_loader
            ->expects($this->any())
            ->method('getCryptoForVersion')
            ->will($this->returnValue($crypto_adapter));

        $historical_upcrypto = new Upcrypto($historical_version_loader);
        $new_upcrypto = new Upcrypto($new_version_loader);

        $historically_encrypted = $historical_upcrypto->encrypt('historically encrypted');

        $this->assertFalse(
            $new_upcrypto->isUpToDate($historically_encrypted)
        );
        $encrypted_with_upgrade = $new_upcrypto->upgradeEncryption(
            $historically_encrypted
        );
        $this->assertTrue(
            $new_upcrypto->isUpToDate($encrypted_with_upgrade)
        );
    }

    public function testUpToDateEncryptionLeavesEncryptionObjectUnchanged()
    {
        $crypto_adapter = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
            )
            ->getMock();

        $version_loader = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoVersionLoader\AbstractCryptoVersionLoader'
            )
            ->setMethods(['getLatestCryptoVersionId', 'getCryptoForVersion'])
            ->getMock();
        $version_loader
            ->expects($this->any())
            ->method('getLatestCryptoVersionId')
            ->will($this->returnValue(2));
        $version_loader
            ->expects($this->any())
            ->method('getCryptoForVersion')
            ->will($this->returnValue($crypto_adapter));

        $upcrypto = new Upcrypto($version_loader);

        $historically_encrypted = $upcrypto->encrypt('historically encrypted');

        $this->assertTrue(
            $upcrypto->isUpToDate($historically_encrypted)
        );
        $encrypted_with_upgrade = $upcrypto->upgradeEncryption(
            $historically_encrypted
        );
        $this->assertTrue(
            $upcrypto->isUpToDate($encrypted_with_upgrade)
        );
        $this->assertEquals(
            $historically_encrypted,
            $encrypted_with_upgrade
        );
    }
}
