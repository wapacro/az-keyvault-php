<?php


namespace AzKeyVault\Responses\Secret;


use AzKeyVault\Contracts\EntityInterface;

class SecretEntity implements EntityInterface {

	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var string
	 */
	public $version;
	/**
	 * @var string
	 */
	public $secret;
	/**
	 * @var string
	 */
	public $url;
	/**
	 * @var SecretAttributeEntity
	 */
	public $attributes;

	public function __construct(string $name, string $version, string $secret, string $url, SecretAttributeEntity $attributes) {
		$this->name = $name;
		$this->version = $version;
		$this->secret = $secret;
		$this->url = $url;
		$this->attributes = $attributes;
	}

	public function __toString() {
		return $this->secret;
	}

}
