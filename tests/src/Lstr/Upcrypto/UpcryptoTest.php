<?php

namespace Lstr\Upcrypto;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Lstr\Upcrypto\Upcrypto
 */
class UpcryptoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::encrypt
     */
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

    /**
     * @covers ::__construct
     * @covers ::decrypt
     * @covers ::<private>
     */
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
     * @covers ::__construct
     * @covers ::decrypt
     * @covers ::<private>
     * @expectedException \Lstr\Upcrypto\Exception
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
     * @covers ::__construct
     * @covers ::decrypt
     * @covers ::<private>
     * @expectedException \Lstr\Upcrypto\Exception
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
     * @covers ::__construct
     * @covers ::decrypt
     * @covers ::<private>
     * @expectedException \Lstr\Upcrypto\Exception
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

    /**
     * @covers ::__construct
     * @covers ::encrypt
     * @covers ::decrypt
     * @covers ::<private>
     */
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

    /**
     * @covers ::__construct
     * @covers ::isUpToDate
     */
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

    /**
     * @covers ::__construct
     * @covers ::isUpToDate
     */
    public function testOutOfDateEncryptionCanBeDetected()
    {
        $crypto_adapter = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface'
            )
            ->getMock();

        $old_version_loader = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoVersionLoader\AbstractCryptoVersionLoader'
            )
            ->setMethods(['getLatestCryptoVersionId', 'getCryptoForVersion'])
            ->getMock();
        $old_version_loader
            ->expects($this->any())
            ->method('getLatestCryptoVersionId')
            ->will($this->returnValue(1));
        $old_version_loader
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

        $historical_upcrypto = new Upcrypto($old_version_loader);
        $new_upcrypto = new Upcrypto($new_version_loader);

        $this->assertFalse(
            $new_upcrypto->isUpToDate(
                $historical_upcrypto->encrypt('historically encrypted')
            )
        );
    }

    /**
     * @covers ::__construct
     * @covers ::upgradeEncryption
     * @covers ::<private>
     */
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

        $old_version_loader = $this
            ->getMockBuilder(
                '\Lstr\Upcrypto\CryptoVersionLoader\AbstractCryptoVersionLoader'
            )
            ->setMethods(['getLatestCryptoVersionId', 'getCryptoForVersion'])
            ->getMock();
        $old_version_loader
            ->expects($this->any())
            ->method('getLatestCryptoVersionId')
            ->will($this->returnValue(1));
        $old_version_loader
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

        $historical_upcrypto = new Upcrypto($old_version_loader);
        $new_upcrypto = new Upcrypto($new_version_loader);

        $old_encryption = $historical_upcrypto->encrypt('historically encrypted');

        $this->assertFalse(
            $new_upcrypto->isUpToDate($old_encryption)
        );
        $new_encryption = $new_upcrypto->upgradeEncryption(
            $old_encryption
        );
        $this->assertTrue(
            $new_upcrypto->isUpToDate($new_encryption)
        );
    }

    /**
     * @covers ::__construct
     * @covers ::upgradeEncryption
     * @covers ::<private>
     */
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

        $old_encryption = $upcrypto->encrypt('historically encrypted');

        $this->assertTrue(
            $upcrypto->isUpToDate($old_encryption)
        );
        $new_encryption = $upcrypto->upgradeEncryption(
            $old_encryption
        );
        $this->assertTrue(
            $upcrypto->isUpToDate($new_encryption)
        );
        $this->assertEquals(
            $old_encryption,
            $new_encryption
        );
    }
}
