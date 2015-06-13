<?php

namespace Lstr\Upcrypto\CryptoVersionLoader;

use Exception;
use Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface;

interface CryptoVersionLoaderInterface
{
    /**
     * Returns the version number of the latest crypto
     * configuration.
     *
     * @return int
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
     * @param int $version
     * @return CryptoAdapterInterface
     */
    public function getCryptoForVersion($version);
}
