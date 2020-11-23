<?php

namespace AzKeyVault\Abstracts;

use AzKeyVault\Client;

abstract class Vault {
    /** @var Client */
    protected $client;

    /** @var string */
    protected $vaultUrl;

    /**
     * Vault constructor
     * @param string|null $url
     * @param null $client
     */
    public function __construct(string $url = null, $client = null) {
        $this->client = $client ?? new Client();

        if ($url) {
            $this->setKeyVault($url);
        }
    }

    /**
     * Set target KeyVault
     * @param string $url
     */
    public function setKeyVault(string $url): void {
        $this->vaultUrl = $url;
    }

    public function getKeyVault(): string {
        return $this->vaultUrl;
    }
}
