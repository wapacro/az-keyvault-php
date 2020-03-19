<?php


namespace AzKeyVault\Responses\Secret;


use AzKeyVault\Contracts\EntityInterface;
use AzKeyVault\Contracts\RepositoryInterface;

class SecretVersionRepository implements RepositoryInterface {

	protected $values = [];

	public function add(EntityInterface $entity) {
		$this->values[] = $entity;
	}

	public function enabled() {
		return array_filter($this->all(), function (SecretVersionEntity $secretVersion) {
			return $secretVersion->attributes->enabled;
		});
	}

	public function all() {
		return $this->values;
	}

}
