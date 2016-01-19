<?php

namespace Lstr\Upcrypto\CryptoAdapter;

use Lstr\Upcrypto\Exception;
use Zend\Crypt\BlockCipher;
use Zend\Crypt\Symmetric\Mcrypt;

class ZendCryptAdapter implements CryptoAdapterInterface
{
    /**
     * @var string
     */
    private $crypto_key;

    /**
     * @var BlockCipher
     */
    private $block_cipher;

    /**
     * @param array $params
     *   - string $params['crypto_key'] - the encryption key
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
        return $this->getBlockCipher()->encrypt($plain_text);
    }

    /**
     * @param string $cipher_text - the text to decrypt
     * @return string - the decrypted text
     */
    public function decrypt($cipher_text)
    {
        return $this->getBlockCipher()->decrypt($cipher_text);
    }

    /**
     * @return BlockCipher
     */
    private function getBlockCipher()
    {
        if ($this->block_cipher) {
            return $this->block_cipher;
        }

        $this->block_cipher = BlockCipher::factory(
            'mcrypt',
            ['padding' => Mcrypt::DEFAULT_PADDING]
        );
        $this->block_cipher->setKey($this->crypto_key);

        return $this->block_cipher;
    }
}
