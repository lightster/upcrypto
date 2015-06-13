<?php

namespace Lstr\Upcrypto;

use Exception;
use Lstr\Upcrypto\CryptoVersionLoader\CryptoVersionLoaderInterface;

class Upcrypto
{
    /**
     * @var CryptoVersionLoaderInterface
     */
    private $version_loader;

    /**
     * @param CryptoVersionLoaderInterface $version_loader
     */
    public function __construct(CryptoVersionLoaderInterface $version_loader)
    {
        $this->version_loader = $version_loader;
    }

    /**
     * @param string $plain_text
     * @return string
     */
    public function encrypt($plain_text)
    {
        $crypto = $this->version_loader->getLatestCrypto();

        $object_info = [
            'crypto_version' => $this->version_loader->getLatestCryptoVersionId(),
            'cipher'         => $crypto->encrypt($plain_text),
        ];

        return json_encode($object_info);
    }

    /**
     * @param string $encryption_object
     * @return string
     */
    public function decrypt($encryption_object)
    {
        $object_info = $this->processEncryptedObject($encryption_object);

        $crypto = $this->version_loader->getCryptoForVersion(
            $object_info['crypto_version']
        );

        return $crypto->decrypt($object_info['cipher']);
    }

    /**
     * @param string $encryption_object
     * @return boolean
     */
    public function isUpToDate($encryption_object)
    {
        $object_info = $this->processEncryptedObject($encryption_object);

        return $this->version_loader->getLatestCryptoVersionId() == $object_info['crypto_version'];
    }

    /**
     * @param string $encryption_object
     * @return string
     */
    public function upgradeEncryption($encryption_object)
    {
        if ($this->isUpToDate($encryption_object)) {
            return $encryption_object;
        }

        return $this->encrypt($this->decrypt($encryption_object));
    }

    /**
     * @param string $encryption_object
     * @return string
     */
    private function processEncryptedObject($encryption_object)
    {
        $object_information = json_decode($encryption_object, true);

        if (empty($object_information)) {
            throw new Exception(
                "The passed encrypted object is invalid."
            );
        }

        if (!array_key_exists('crypto_version', $object_information)) {
            throw new Exception(
                "The crypto version for the encrypted object does not exist."
            );
        }

        if (!array_key_exists('cipher', $object_information)) {
            throw new Exception(
                "The cipher for the encrypted object could not be determined."
            );
        }

        return $object_information;
    }
}
