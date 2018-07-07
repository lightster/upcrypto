<?php

namespace Lstr\Upcrypto\CryptoAdapter;

use PHPUnit\Framework\TestCase;

abstract class AbstractAdapterTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::encrypt
     * @covers ::decrypt
     * @covers ::<private>
     */
    public function testEncryptingAndDecryptingReturnsTheOriginalString()
    {
        $adapter = $this->getGoodCryptoAdapter();

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
        $encryption_adapter = $this->getGoodCryptoAdapter();
        $decryption_adapter = $this->getBadCryptoAdapter();

        $known_string = 'this string is known';
        $this->assertFalse(
            $decryption_adapter->decrypt(
                $encryption_adapter->encrypt($known_string)
            )
        );
    }

    /**
     * @return CryptoAdapterInterface
     */
    abstract protected function getGoodCryptoAdapter();

    /**
     * @return CryptoAdapterInterface
     */
    abstract protected function getBadCryptoAdapter();
}
