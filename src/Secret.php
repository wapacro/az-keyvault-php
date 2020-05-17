<?php


namespace AzKeyVault;


use AzKeyVault\Responses\Secret\SecretAttributeEntity;
use AzKeyVault\Responses\Secret\SecretEntity;
use AzKeyVault\Responses\Secret\SecretVersionEntity;
use AzKeyVault\Responses\Secret\SecretVersionRepository;
use Spatie\Url\Url;

class Secret extends Vault {

	/**
	 * Returns all versions for given secret
	 * @param string $secretName
	 * @return SecretVersionRepository
	 */
	public function getSecretVersions(string $secretName) {
		$endpoint = Url::fromString($this->vaultUrl)->withPath(sprintf('/secrets/%s/versions', $secretName));
		$response = $this->client->get($endpoint);
		$secretVersionRepository = new SecretVersionRepository();

		foreach ($response->value as $version) {
			$secretVersion = new SecretVersionEntity(
				$secretName,
				Url::fromString($version->id)->getLastSegment(),
				$version->id,
				new SecretAttributeEntity(
					$version->attributes->enabled,
					$version->attributes->created,
					$version->attributes->updated,
					$version->attributes->recoveryLevel,
					isset($version->attributes->exp) ? $version->attributes->exp : null,
					isset($version->attributes->nbf) ? $version->attributes->nbf : null,
				),
				isset($version->contentType) ? $version->contentType : null,
			);

			$secretVersionRepository->add($secretVersion);
		}

		return $secretVersionRepository;
	}

	/**
	 * Returns the value for given secret,
	 * either by passing an instance of
	 * SecretVersionEntity or by secret
	 * name and version
	 * @param SecretVersionEntity|string $secret
	 * @param string|null $secretVersion
	 * @return SecretEntity
	 */
	public function getSecret($secret, string $secretVersion = null) {
		if ($secret instanceof SecretVersionEntity && !$secretVersion) {
			$secretVersion = $secret->id;
			$secret = $secret->name;
		}

		$endpoint = Url::fromString($this->vaultUrl)->withPath(sprintf('/secrets/%s/%s', $secret, $secretVersion));
		$response = $this->client->get($endpoint);

		return new SecretEntity(
			$secret,
			$secretVersion,
			$response->value,
			$response->id,
			new SecretAttributeEntity(
				$response->attributes->enabled,
				$response->attributes->created,
				$response->attributes->updated,
				$response->attributes->recoveryLevel,
				isset($response->attributes->exp) ? $response->attributes->exp : null,
				isset($response->attributes->nbf) ? $response->attributes->nbf : null,
			),
			isset($response->contentType) ? $response->contentType : null,
		);
	}

}
