<?php

namespace Lstr\Upcrypto\CryptoAdapter;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lstr\Upcrypto\CryptoAdapter\ZendCryptAdapter
 */
class ZendCryptAdapterTest extends AbstractAdapterTest
{
    public function setUp()
    {
        if (version_compare(PHP_VERSION, '7.1') >= 0) {
            $this->markTestSkipped(
                'mcrypt (and therefore the Zend\Crypt adapter) is only'
                    . ' supported below PHP 7.1'
            );
        }
    }

    /**
     * @covers ::__construct
     * @expectedException \Lstr\Upcrypto\Exception
     */
    public function testFailingToProvideAKeyThrowsAnException()
    {
        new ZendCryptAdapter([]);
    }

    /**
     * @return ZendCryptAdapter
     */
    protected function getGoodCryptoAdapter()
    {
        return new ZendCryptAdapter([
            'crypto_key' => 'the correct password!',
        ]);
    }

    /**
     * @return ZendCryptAdapter
     */
    protected function getBadCryptoAdapter()
    {
        return new ZendCryptAdapter([
            'crypto_key' => 'the incorrect password!',
        ]);
    }
}
