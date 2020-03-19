<?php


namespace AzKeyVault;


use AzKeyVault\Responses\Secret\SecretAttributeEntity;
use AzKeyVault\Responses\Secret\SecretEntity;
use AzKeyVault\Responses\Secret\SecretVersionEntity;
use AzKeyVault\Responses\Secret\SecretVersionRepository;
use GuzzleHttp\Client;
use Spatie\Url\Url;

class Secret {

	private $accessToken;
	private $client;
	private $keyVaultUrl;

	public function __construct(string $url = null) {
		$this->client = new Client();
		$this->accessToken = (new Identity())->getAccessToken();

		if ($url) {
			$this->setKeyVault($url);
		}
	}

	public function setKeyVault(string $url) {
		$this->keyVaultUrl = $url;
	}

	/**
	 * Returns all versions for given secret
	 * @param string $secretName
	 * @return SecretVersionRepository
	 */
	public function getSecretVersions(string $secretName) {
		$endpoint = Url::fromString($this->keyVaultUrl)
			->withPath(sprintf('/secrets/%s/versions', $secretName))
			->withQueryParameter('api-version', '7.0');

		$response = $this->client->get($endpoint, [
			'headers' => [
				'Authorization' => 'Bearer ' . $this->accessToken,
			],
		]);

		$response = json_decode($response->getBody());
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
				),
			);

			$secretVersionRepository->add($secretVersion);
		}

		return $secretVersionRepository;
	}

	public function getSecret(SecretVersionEntity $secret, string $secretVersion = null) {
		if ($secret instanceof SecretVersionEntity && !$secretVersion) {
			$secretVersion = $secret->id;
			$secret = $secret->name;
		}

		$endpoint = Url::fromString($this->keyVaultUrl)
			->withPath(sprintf('/secrets/%s/%s', $secret, $secretVersion))
			->withQueryParameter('api-version', '7.0');

		$response = $this->client->get($endpoint, [
			'headers' => [
				'Authorization' => 'Bearer ' . $this->accessToken,
			],
		]);

		$response = json_decode($response->getBody());
		return new SecretEntity(
			$secret,
			$secretVersion,
			$response->value,
			$response->id,
			new SecretAttributeEntity(
				$response->attributes->enabled,
				$response->attributes->created,
				$response->attributes->updated,
				$response->attributes->recoveryLevel
			)
		);
	}

}
