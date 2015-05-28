<?php

namespace Lstr\Upcrypto\CryptoVersionLoader;

use Exception;
use Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface;

class ArrayCryptoVersionLoader extends AbstractCryptoVersionLoader
{
    /**
     * @var $versions
     */
    private $versions;

    /**
     * @var $versions
     */
    private $cryptos;

    /**
     * @param array $versions
     */
    public function __construct(array $versions)
    {
        $this->versions = $versions;
    }

    /**
     * Returns the version number of the latest crypto
     * configuration.
     *
     * @return int
     */
    public function getLatestCryptoVersionNumber()
    {
        return count($this->versions);
    }

    /**
     * Returns a crypto adapter for the given crypto
     * configuration version number.
     *
     * @param int $version
     * @return CryptoAdapterInterface
     */
    public function getCryptoForVersion($version)
    {
        $version_offset = $version - 1;

        if (!isset($this->versions[$version_offset])) {
            throw new Exception(
                "Unknown crypto version '{$version_offset}'."
            );
        }

        if (isset($this->cryptos[$version_offset])) {
            return $this->cryptos[$version_offset];
        }

        $version = $this->versions[$version_offset];
        $crypto_class = $version['crypto_adapter'];
        $crypto = new $crypto_class($version);

        $this->cryptos[$version_offset] = $crypto;

        return $this->cryptos[$version_offset];
    }
}
