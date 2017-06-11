<?php

namespace Lstr\Upcrypto\CryptoAdapter;

use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Lstr\Upcrypto\Exception;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

class DefuseAdapter implements CryptoAdapterInterface
{
    /**
     * @var string
     */
    private $crypto_key;

    /**
     * @var Key
     */
    private $raw_key;

    /**
     * @param array $params
     *   - string $params['crypto_key'] - the encryption key
     * @throws Exception
     */
    public function __construct(array $params)
    {
        if (!array_key_exists('crypto_key', $params)) {
            throw new Exception(
                __CLASS__ . " requires a 'crypto_key' parameter."
            );
        }

        $this->crypto_key = $params['crypto_key'];
    }

    /**
     * @param string $plain_text - the text to encrypt
     * @return string - the encrypted text
     */
    public function encrypt($plain_text)
    {
        return Crypto::encrypt($plain_text, $this->getRawKey());
    }

    /**
     * @param string $cipher_text - the text to decrypt
     * @return string - the decrypted text
     */
    public function decrypt($cipher_text)
    {
        try {
            return Crypto::decrypt($cipher_text, $this->getRawKey());
        } catch (WrongKeyOrModifiedCiphertextException $ex) {
            return false;
        }
    }

    /**
     * @return Key
     */
    private function getRawKey()
    {
        if ($this->raw_key) {
            return $this->raw_key;
        }

        $this->raw_key = Key::loadFromAsciiSafeString($this->crypto_key);

        return $this->raw_key;
    }
}
