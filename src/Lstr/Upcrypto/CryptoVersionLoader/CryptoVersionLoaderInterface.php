<?php

namespace Lstr\Upcrypto\CryptoVersionLoader;

use Exception;

interface CryptoVersionLoaderInterface
{
    public function getLatestCryptoVersionNumber();
    public function getLatestCrypto();
    public function getCryptoForVersion();
}
