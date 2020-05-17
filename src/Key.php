<?php


namespace AzKeyVault;


use AzKeyVault\Abstracts\Vault;
use AzKeyVault\Responses\Key\KeyAttributeEntity;
use AzKeyVault\Responses\Key\KeyEntity;
use AzKeyVault\Responses\Key\KeyVersionEntity;
use AzKeyVault\Responses\Key\KeyVersionRepository;
use Spatie\Url\Url;

class Key extends Vault {

	/**
	 * Returns all versions for given key
	 * @param string $keyName
	 * @return KeyVersionRepository
	 */
	public function getKeyVersions(string $keyName) {
		$endpoint = Url::fromString($this->vaultUrl)->withPath(sprintf('/keys/%s/versions', $keyName));
		$response = $this->client->get($endpoint);
		$keyVersionRepository = new KeyVersionRepository();

		foreach ($response->value as $version) {
			$keyVersion = new KeyVersionEntity(
				$keyName,
				Url::fromString($version->kid)->getLastSegment(),
				$version->kid,
				new KeyAttributeEntity(
					$version->attributes->enabled,
					$version->attributes->created,
					$version->attributes->updated,
					$version->attributes->recoveryLevel,
					isset($version->attributes->exp) ? $version->attributes->exp : null,
					isset($version->attributes->nbf) ? $version->attributes->nbf : null,
				)
			);

			$keyVersionRepository->add($keyVersion);
		}

		return $keyVersionRepository;
	}

	/**
	 * Returns the value for given key,
	 * either by passing an instance of
	 * KeyVersionEntity or by secret
	 * name and version
	 * @param KeyVersionEntity|string $key
	 * @param string|null $keyVersion
	 * @return KeyEntity
	 */
	public function getKey($key, string $keyVersion = null) {
		if ($key instanceof KeyVersionEntity && !$keyVersion) {
			$keyVersion = $key->id;
			$key = $key->name;
		}

		$endpoint = Url::fromString($this->vaultUrl)->withPath(sprintf('/keys/%s/%s', $key, $keyVersion));
		$response = $this->client->get($endpoint);

		return new KeyEntity(
			$key,
			$keyVersion,
			$response->key->kid,
			$response->key->kty,
			$response->key->key_ops,
			new KeyAttributeEntity(
				$response->attributes->enabled,
				$response->attributes->created,
				$response->attributes->updated,
				$response->attributes->recoveryLevel,
				isset($response->attributes->exp) ? $response->attributes->exp : null,
				isset($response->attributes->nbf) ? $response->attributes->nbf : null,
			),
			isset($response->key->key_hsm) ? $response->key->key_hsm : null,
			isset($response->key->crv) ? $response->key->crv : null,
			isset($response->key->x) ? $response->key->x : null,
			isset($response->key->y) ? $response->key->y : null,
			isset($response->key->d) ? $response->key->d : null,
			isset($response->key->dp) ? $response->key->dp : null,
			isset($response->key->dq) ? $response->key->dq : null,
			isset($response->key->e) ? $response->key->e : null,
			isset($response->key->n) ? $response->key->n : null,
			isset($response->key->p) ? $response->key->p : null,
			isset($response->key->q) ? $response->key->q : null,
			isset($response->key->qi) ? $response->key->qi : null,
			isset($response->key->k) ? $response->key->k : null,
		);
	}

}
