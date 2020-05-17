<?php

namespace AzKeyVault\Abstracts;

abstract class Vault {

	protected $client;
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
	public function setKeyVault(string $url) {
		$this->vaultUrl = $url;
	}

}
