<?php

namespace Lstr\Upcrypto\CryptoVersionLoader;

use Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface;

abstract class AbstractCryptoVersionLoader implements CryptoVersionLoaderInterface
{
    /**
     * Returns a crypto adapter for the latest crypto
     * configuration.
     *
     * @return CryptoAdapterInterface
     */
    public function getLatestCrypto()
    {
        return $this->getCryptoForVersion($this->getLatestCryptoVersionId());
    }
}
