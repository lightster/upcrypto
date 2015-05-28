<?php

namespace Lstr\Upcrypto\CryptoAdapter;

interface CryptoAdapterInterface
{
    /**
     * @param string $plain_text - the text to encrypt
     * @return string - the encrypted text
     */
    public function encrypt($plain_text);

    /**
     * @param string $cipher_text - the text to decrypt
     * @return string - the decrypted text
     */
    public function decrypt($cipher_text);
}
