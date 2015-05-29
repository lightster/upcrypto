<?php

namespace Lstr\Upcrypto\CryptoAdapter;

use PHPUnit_Framework_TestCase;

class ZendCryptAdapterTest extends PHPUnit_Framework_TestCase
{
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
     * @expectedException \Exception
     */
    public function testFailingToProvideAKeyThrowsAnException()
    {
        $adapter = new ZendCryptAdapter([]);
    }
}
