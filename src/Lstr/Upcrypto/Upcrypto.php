<?php

namespace Lstr\Upcrypto;

use Exception;
use Lstr\Upcrypto\CryptoVersionLoader\CryptoVersionLoaderInterface;

class Upcrypto
{
    public function __construct(CryptoVersionLoaderInterface $version_loader)
    {
        $this->version_loader = $version_loader;
    }

    public function encrypt($plain_text)
    {
    }

    public function decrypt($encryption_object)
    {
    }

    public function isUpToDate($encryption_object)
    {
    }

    public function upgradeEncryption($encryption_object)
    {
    }
}
