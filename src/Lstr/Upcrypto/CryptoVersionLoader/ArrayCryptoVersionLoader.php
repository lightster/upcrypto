<?php

namespace Lstr\Upcrypto\CryptoVersionLoader;

use Lstr\Upcrypto\Exception;
use Lstr\Upcrypto\CryptoAdapter\CryptoAdapterInterface;

class ArrayCryptoVersionLoader extends AbstractCryptoVersionLoader
{
    /**
     * @var array
     */
    private $versions;

    /**
     * @var array
     */
    private $cryptos;

    /**
     * @var array
     */
    private $latest_version_id;

    /**
     * @param array $versions
     */
    public function __construct(array $versions)
    {
        $this->versions = $versions;
    }

    /**
     * Returns the version ID of the latest crypto
     * configuration.
     *
     * @return string
     */
    public function getLatestCryptoVersionId()
    {
        if (null !== $this->latest_version_id) {
            return $this->latest_version_id;
        }

        end($this->versions);
        $this->latest_version_id = key($this->versions);

        return $this->latest_version_id;
    }

    /**
     * Returns a crypto adapter for the given crypto
     * configuration version number.
     *
     * @param string $version_id
     * @return CryptoAdapterInterface
     */
    public function getCryptoForVersion($version_id)
    {
        if (!isset($this->versions[$version_id])) {
            throw new Exception(
                "Unknown crypto version '{$version_id}'."
            );
        }

        if (isset($this->cryptos[$version_id])) {
            return $this->cryptos[$version_id];
        }

        $version = $this->versions[$version_id];
        $crypto_class = $version['crypto_adapter'];
        $crypto = new $crypto_class($version);

        $this->cryptos[$version_id] = $crypto;

        return $this->cryptos[$version_id];
    }
}
