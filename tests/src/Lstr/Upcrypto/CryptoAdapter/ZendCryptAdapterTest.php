<?php

namespace Lstr\Upcrypto\CryptoAdapter;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Lstr\Upcrypto\CryptoAdapter\ZendCryptAdapter
 */
class ZendCryptAdapterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::encrypt
     * @covers ::decrypt
     * @covers ::<private>
     */
    public function testEncryptingAndDecryptingReturnsTheOriginalString()
    {
        $adapter = new ZendCryptAdapter([
            'crypto_key' => 'the correct password!',
        ]);

        $known_string = 'this string is known';
        $this->assertEquals(
            $known_string,
            $adapter->decrypt($adapter->encrypt($known_string))
        );
    }

    /**
     * @covers ::__construct
     * @covers ::encrypt
     * @covers ::decrypt
     * @covers ::<private>
     */
    public function testUsingTheWrongDecryptionKeyThrowsAnException()
    {
        $encryption_adapter = new ZendCryptAdapter([
            'crypto_key' => 'the correct password!',
        ]);
        $decryption_adapter = new ZendCryptAdapter([
            'crypto_key' => 'the incorrect password!',
        ]);

        $known_string = 'this string is known';
        $this->assertFalse(
            $decryption_adapter->decrypt(
                $encryption_adapter->encrypt($known_string)
            )
        );
    }

    /**
     * @covers ::__construct
     * @expectedException \Lstr\Upcrypto\Exception
     */
    public function testFailingToProvideAKeyThrowsAnException()
    {
        new ZendCryptAdapter([]);
    }
}
