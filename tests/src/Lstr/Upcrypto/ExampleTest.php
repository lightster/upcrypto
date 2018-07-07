<?php

namespace Lstr\Upcrypto;

use Lstr\Upcrypto\CryptoVersionLoader\ArrayCryptoVersionLoader;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testExampleRuns()
    {
        $versions = [
            '2017.01.31' => [
                'crypto_adapter' => '\Lstr\Upcrypto\CryptoAdapter\DefuseAdapter',
                'crypto_key' => 'def000007abb9fab43861eb5a2579460cfcbec7774ecb84bf7ddd3379ebdcf52b6c2'
                    . '2955049147eac6aa93ebfa6d2452650b222b1def408fe9c58ea527544a41602fe44f',
            ],
            '2017.05.19' => [
                'crypto_adapter' => '\Lstr\Upcrypto\CryptoAdapter\DefuseAdapter',
                'crypto_key' => 'def00000d988a0280b5580151c2f207934b6abcd5e59e2cab81c11214f6e2e84c1a8'
                    . '1627fb3e9634ae1c36d3958ca2491fb95a1337a897338030e77662b38a7cf7fc9dcd',
            ],
        ];

        $old_version_loader = new ArrayCryptoVersionLoader([
            '2017.01.31' => $versions['2017.01.31']
        ]);
        $original_upcrypto = new Upcrypto($old_version_loader);
        $loaded_value = $original_upcrypto->encrypt('tada');
        // pretend the encrypted $loaded_value is actually stored in a database
        // and we just read the encrypted value from the database into $loaded_value

        $version_loader = new ArrayCryptoVersionLoader([
            '2017.01.31' => $versions['2017.01.31'],
            '2017.05.19' => $versions['2017.05.19'],
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
