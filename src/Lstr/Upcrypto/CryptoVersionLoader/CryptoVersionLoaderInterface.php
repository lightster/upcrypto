<?php

namespace Lstr\Upcrypto\CryptoVersionLoader;

use Lstr\Upcrypto\Exception;
use Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface;

interface CryptoVersionLoaderInterface
{
    /**
     * Returns the version number of the latest crypto
     * configuration.
     *
     * @return string
     */
    public function getLatestCryptoVersionId();

    /**
     * Returns a crypto adapter for the latest crypto
     * configuration.
     *
     * @return CryptoAdapterInterface
     */
    public function getLatestCrypto();

    /**
     * Returns a crypto adapter for the given crypto
     * configuration version number.
     *
     * @param string $version
     * @return CryptoAdapterInterface
     */
    public function getCryptoForVersion($version);
}
