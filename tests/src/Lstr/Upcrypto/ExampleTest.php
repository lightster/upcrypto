<?php

namespace Lstr\Upcrypto;

use Lstr\Upcrypto\CryptoVersionLoader\ArrayCryptoVersionLoader;
use PHPUnit_Framework_TestCase;

class ExampleTest extends PHPUnit_Framework_TestCase
{
    public function testZendCryptHasAnExample()
    {
        $versions = [
            '2015.01.31' => [
                'crypto_adapter' => '\Lstr\Upcrypto\CryptoAdapter\ZendCryptAdapter',
                'crypto_key' => 'the original key',
            ],
            '2015.05.19' => [
                'crypto_adapter' => '\Lstr\Upcrypto\CryptoAdapter\ZendCryptAdapter',
                'crypto_key' => 'the new and improved key',
            ]
        ];

        $old_version_loader = new ArrayCryptoVersionLoader([
            '2015.01.31' => $versions['2015.01.31']
        ]);
        $original_upcrypto = new Upcrypto($old_version_loader);
        $loaded_value = $original_upcrypto->encrypt('tada');
        // pretend the encrypted $loaded_value is actually stored in a database
        // and we just read the encrypted value from the database into $loaded_value

        $version_loader = new ArrayCryptoVersionLoader([
            '2015.01.31' => $versions['2015.01.31'],
            '2015.05.19' => $versions['2015.05.19'],
        ]);
        $upcrypto = new Upcrypto($version_loader);

        // if you just want to decrypt the value
        $this->assertEquals(
            'tada',
            $upcrypto->decrypt($loaded_value)
        );

        // if we want to check if the encryption is our most current
        // method of encryption
        if (!$upcrypto->isUpToDate($loaded_value)) {
            // if it is not, we can upgrade it to the latest methodology
            $new_encryption = $upcrypto->upgradeEncryption($loaded_value);
            // now you can save the data back to the database

            // the upgraded version still decrypts to the propery value
            $this->assertEquals(
                'tada',
                $upcrypto->decrypt($new_encryption)
            );
            // and the original encryption cipher is not the same as the
            // new encrpytion cipher
            $this->assertNotEquals(
                $loaded_value,
                $new_encryption
            );
        } else {
            $this->assertFalse(true);
        }
    }
}
