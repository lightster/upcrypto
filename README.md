
# upcrypto

[![Build Status](https://travis-ci.org/lightster/upcrypto.svg?branch=master)](https://travis-ci.org/lightster/upcrypto)
[![Test Coverage](https://codeclimate.com/github/lightster/upcrypto/badges/coverage.svg)](https://codeclimate.com/github/lightster/upcrypto/coverage)
[![Code Climate](https://codeclimate.com/github/lightster/upcrypto/badges/gpa.svg)](https://codeclimate.com/github/lightster/upcrypto)
[![Dependency Status](https://www.versioneye.com/user/projects/532e6d03f599491449000238/badge.svg?style=flat)](https://www.versioneye.com/user/projects/532e6d03f599491449000238)


Streamline runtime upgrade of cryptography

Upcrypto allows for changing of encryption keys or encryption methods without
affecting historical data and with the ability to upgrade encrypted data over
time.

## Example

```php
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

$original_version_loader = new ArrayCryptoVersionLoader([
    '2015.01.31' => $versions['2015.01.31']
]);
$original_upcrypto = new Upcrypto($original_version_loader);
$loaded_value = $original_upcrypto->encrypt('tada');
// pretend the encrypted $loaded_value is actually stored in a database
// and we just read the encrypted value from the database into $loaded_value

$version_loader = new ArrayCryptoVersionLoader([
    '2015.01.31' => $versions['2015.01.31'],
    '2015.05.19' => $versions['2015.05.19'],
]);
$upcrypto = new Upcrypto($version_loader);

// if you just want to decrypt the value
$original_decrypted = $upcrypto->decrypt($loaded_value);

// if we want to check if the encryption is our most current
// method of encryption
if (!$upcrypto->isUpToDate($loaded_value)) {
    // if it is not, we can upgrade it to the latest methodology
    $upgraded_encrypted_value = $upcrypto->upgradeEncryption($loaded_value);
    // now you can save the data back to the database

    // the upgraded version still decrypts to the propery value
    $newly_decrypted = $upcrypto->decrypt($upgraded_encrypted_value);
}
```
