<?php


namespace AzKeyVault;

use Spatie\Url\Url;
use AzKeyVault\Abstracts\Vault;
use AzKeyVault\Responses\Key\KeyEntity;
use AzKeyVault\Responses\Key\KeyVersionEntity;
use AzKeyVault\Responses\Key\KeyAttributeEntity;
use AzKeyVault\Responses\Key\KeyVersionRepository;

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
                    $version->attributes->exp ?? null,
                    $version->attributes->nbf ?? null,
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
                $response->attributes->exp ?? null,
                $response->attributes->nbf ?? null,
            ),
            $response->key->crv ?? null,
            $response->key->x ?? null,
            $response->key->y ?? null,
            $response->key->e ?? null,
            $response->key->n ?? null,
        );
    }
}
