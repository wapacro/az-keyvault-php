<?php


namespace AzKeyVault;

use Spatie\Url\Url;
use AzKeyVault\Abstracts\Vault;
use AzKeyVault\Responses\IdEntity;
use AzKeyVault\Responses\IdRepository;
use AzKeyVault\Responses\Secret\SecretEntity;
use AzKeyVault\Responses\Secret\SecretVersionEntity;
use AzKeyVault\Responses\Secret\SecretAttributeEntity;
use AzKeyVault\Responses\Secret\SecretVersionRepository;

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
                    $version->attributes->exp ?? null,
                    $version->attributes->nbf ?? null,
                ),
                $version->contentType ?? null,
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

        // Set secretVersion if not provide.
        if ($secretVersion === null) {
            $secretVersion = Url::fromString($response->id)->getLastSegment();
        }

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
                $response->attributes->exp ?? null,
                $response->attributes->nbf ?? null,
            ),
            $response->contentType ?? null,
        );
    }

    /**
     * Returns list of secrets for current vault
     * @param string|null $nextLink
     * @return IdRepository
     */
    public function getSecrets(string $nextLink = null): IdRepository {
        // Handle the nextLink paging
        // https://docs.microsoft.com/en-us/rest/api/azure/#async-operations-throttling-and-paging
        if ($nextLink !== null) {
            $endpoint = Url::fromString($nextLink);
        } else {
            $endpoint = Url::fromString($this->vaultUrl)->withPath('/secrets');
        }

        $response = $this->client->get($endpoint);
        $idRepository = new IdRepository();

        foreach ($response->value as $secret) {
            $secretId = new IdEntity(
                $secret->id,
                new SecretAttributeEntity(
                    $secret->attributes->enabled,
                    $secret->attributes->created,
                    $secret->attributes->updated,
                    $secret->attributes->recoveryLevel,
                    $secret->attributes->exp ?? null,
                    $secret->attributes->nbf ?? null,
                ),
                $secret->contentType ?? null,
            );

            $idRepository->add($secretId);
        }
        $idRepository->setNextLink($response->nextLink);

        return $idRepository;
    }

    /**
     * Sets a secret in a specified key vault.
	 * If the named secret already exists, Azure Key Vault creates a new version of that secret.
     * @param string $secretName
     * @param string $value
     * @param SecretAttributeEntity|null $secretAttributes
     * @param string|null $contentType
     * @param array|null $tags
     * @return SecretEntity
     */
    public function setSecret(string $secretName, string $value, $secretAttributes = null, string $contentType = null, array $tags = null) {
        $endpoint = Url::fromString($this->vaultUrl)->withPath(sprintf('/secrets/%s', $secretName));
        $body = ['value' => $value];
        if ($secretAttributes instanceOf SecretAttributeEntity) {
			$body['attributes'] = $secretAttributes;
		}
        if (!$contentType) {
			$body['contentType'] = $contentType;
		}
        if (!$tags) {
			$body['tags'] = $tags;
		}
        $response = $this->client->post($endpoint, $body);

        $secretVersion = Url::fromString($response->id)->getLastSegment();

        return new SecretEntity(
            $secretName,
            $secretVersion,
            $response->value,
            $response->id,
            new SecretAttributeEntity(
                $response->attributes->enabled,
                $response->attributes->created,
                $response->attributes->updated,
                $response->attributes->recoveryLevel,
                $response->attributes->exp ?? null,
                $response->attributes->nbf ?? null,
            ),
            $response->contentType ?? null,
        );
    }
}
