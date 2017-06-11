<?php

namespace Lstr\Upcrypto\CryptoAdapter;

/**
 * @coversDefaultClass \Lstr\Upcrypto\CryptoAdapter\DefuseAdapter
 */
class DefuseAdapterTest extends AbstractAdapterTest
{
    /**
     * @covers ::__construct
     * @expectedException \Lstr\Upcrypto\Exception
     */
    public function testFailingToProvideAKeyThrowsAnException()
    {
        new DefuseAdapter([]);
    }

    /**
     * @return DefuseAdapter
     */
    protected function getGoodCryptoAdapter()
    {
        return new DefuseAdapter([
            'crypto_key' => 'def000007abb9fab43861eb5a2579460cfcbec7774ecb84bf7ddd3379ebdcf52b6c2'
                . '2955049147eac6aa93ebfa6d2452650b222b1def408fe9c58ea527544a41602fe44f',
        ]);
    }

    /**
     * @return DefuseAdapter
     */
    protected function getBadCryptoAdapter()
    {
        return new DefuseAdapter([
            'crypto_key' => 'def00000d988a0280b5580151c2f207934b6abcd5e59e2cab81c11214f6e2e84c1a8'
                . '1627fb3e9634ae1c36d3958ca2491fb95a1337a897338030e77662b38a7cf7fc9dcd',
        ]);
    }
}
