<?php

namespace Lstr\Upcrypto\CryptoAdapter;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Lstr\Upcrypto\CryptoAdapter\DefuseAdapter
 */
class DefuseAdapterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::encrypt
     * @covers ::decrypt
     * @covers ::<private>
     */
    public function testEncryptingAndDecryptingReturnsTheOriginalString()
    {
        $adapter = new DefuseAdapter([
            'crypto_key' => 'def000007abb9fab43861eb5a2579460cfcbec7774ecb84bf7ddd3379ebdcf52b6c'
                . '22955049147eac6aa93ebfa6d2452650b222b1def408fe9c58ea527544a41602fe44f',
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
        $encryption_adapter = new DefuseAdapter([
            'crypto_key' => 'def000007abb9fab43861eb5a2579460cfcbec7774ecb84bf7ddd3379ebdcf52b6c2'
                . '2955049147eac6aa93ebfa6d2452650b222b1def408fe9c58ea527544a41602fe44f',
        ]);
        $decryption_adapter = new DefuseAdapter([
            'crypto_key' => 'def00000d988a0280b5580151c2f207934b6abcd5e59e2cab81c11214f6e2e84c1a8'
                . '1627fb3e9634ae1c36d3958ca2491fb95a1337a897338030e77662b38a7cf7fc9dcd',
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
        new DefuseAdapter([]);
    }
}
