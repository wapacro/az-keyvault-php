<?php

namespace AzKeyVault\Tests;

use AzKeyVault\Client;
use AzKeyVault\Secret;
use PHPUnit\Framework\TestCase;
use AzKeyVault\Responses\IdEntity;
use AzKeyVault\Responses\IdRepository;
use AzKeyVault\Responses\Secret\SecretEntity;
use AzKeyVault\Responses\Secret\SecretVersionEntity;
use AzKeyVault\Responses\Secret\SecretAttributeEntity;
use AzKeyVault\Responses\Secret\SecretVersionRepository;

class SecretTest extends TestCase {
    protected $clientMock;

    protected function setUp(): void {
        $this->clientMock = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * A secret should be available by
     * explicitly specifying name and version
     */
    public function testGetSecretByNameAndVersion(): void {
        $this->clientMock->method('get')->willReturn(json_decode(json_encode([
            'value' => 'mysecret',
            'id' => 'https://kv-sdk-test.vault-int.azure-int.net/secrets/mysecretname/4387e9f3d6e14c459867679a90fd0f79',
            'attributes' => [
                'enabled' => true,
                'created' => 1493938410,
                'updated' => 1493938410,
                'recoveryLevel' => 'Recoverable+Purgeable',
            ],
        ])));

        $expectedSecretEntity = new SecretEntity(
            'mysecretname',
            '4387e9f3d6e14c459867679a90fd0f79',
            'mysecret',
            'https://kv-sdk-test.vault-int.azure-int.net/secrets/mysecretname/4387e9f3d6e14c459867679a90fd0f79',
            new SecretAttributeEntity(
                true,
                1493938410,
                1493938410,
                'Recoverable+Purgeable',
            )
        );

        $secret = new Secret('https://kv-sdk-test.vault-int.azure-int.net', $this->clientMock);
        $this->assertEquals($expectedSecretEntity, $secret->getSecret('mysecretname', '4387e9f3d6e14c459867679a90fd0f79'));
    }

    /**
     * Given a secret name all
     * versions should be returned
     * @return SecretVersionEntity
     */
    public function testGetSecretVersions() {
        $this->clientMock->method('get')->willReturn(json_decode(json_encode([
            'value' => [[
                'id' => 'https://kv-sdk-test.vault-int.azure-int.net/secrets/mysecretname/4387e9f3d6e14c459867679a90fd0f79',
                'attributes' => [
                    'enabled' => true,
                    'created' => 1493938410,
                    'updated' => 1493938410,
                    'recoveryLevel' => 'Recoverable+Purgeable',
                ], ],
            ],
        ])));

        $expectedSecretVersionRepository = new SecretVersionRepository();
        $expectedSecretVersionRepository->add($entity = new SecretVersionEntity(
            'mysecretname',
            '4387e9f3d6e14c459867679a90fd0f79',
            'https://kv-sdk-test.vault-int.azure-int.net/secrets/mysecretname/4387e9f3d6e14c459867679a90fd0f79',
            new SecretAttributeEntity(
                true,
                1493938410,
                1493938410,
                'Recoverable+Purgeable',
            ),
        ));

        $secret = new Secret('https://kv-sdk-test.vault-int.azure-int.net', $this->clientMock);
        $this->assertEquals($expectedSecretVersionRepository, $secret->getSecretVersions('mysecretname'));

        return $entity;
    }

    /**
     * A secret should be returned by
     * passing an instance of SecretVersionEntity
     * @depends testGetSecretVersions
     * @param $versionReference
     */
    public function testGetSecretByVersionReference($versionReference): void {
        $this->clientMock->method('get')->willReturn(json_decode(json_encode([
            'value' => 'mysecret',
            'id' => 'https://kv-sdk-test.vault-int.azure-int.net/secrets/mysecretname/4387e9f3d6e14c459867679a90fd0f79',
            'attributes' => [
                'enabled' => true,
                'created' => 1493938410,
                'updated' => 1493938410,
                'recoveryLevel' => 'Recoverable+Purgeable',
            ],
        ])));

        $expectedSecretEntity = new SecretEntity(
            'mysecretname',
            '4387e9f3d6e14c459867679a90fd0f79',
            'mysecret',
            'https://kv-sdk-test.vault-int.azure-int.net/secrets/mysecretname/4387e9f3d6e14c459867679a90fd0f79',
            new SecretAttributeEntity(
                true,
                1493938410,
                1493938410,
                'Recoverable+Purgeable',
            )
        );

        $secret = new Secret('https://kv-sdk-test.vault-int.azure-int.net', $this->clientMock);
        $this->assertEquals($expectedSecretEntity, $secret->getSecret($versionReference));
    }

    /**
     * A secret should be available by
     * explicitly specifying name for the latest version
     */
    public function testGetSecretByName(): void {
        $this->clientMock->method('get')->willReturn(json_decode(json_encode([
            'value' => 'mysecret',
            'id' => 'https://kv-sdk-test.vault-int.azure-int.net/secrets/mysecretname/4387e9f3d6e14c459867679a90fd0f79',
            'attributes' => [
                'enabled' => true,
                'created' => 1493938410,
                'updated' => 1493938410,
                'recoveryLevel' => 'Recoverable+Purgeable',
            ],
        ])));

        $expectedSecretEntity = new SecretEntity(
            'mysecretname',
            '4387e9f3d6e14c459867679a90fd0f79',
            'mysecret',
            'https://kv-sdk-test.vault-int.azure-int.net/secrets/mysecretname/4387e9f3d6e14c459867679a90fd0f79',
            new SecretAttributeEntity(
                true,
                1493938410,
                1493938410,
                'Recoverable+Purgeable',
            )
        );

        $secret = new Secret('https://kv-sdk-test.vault-int.azure-int.net', $this->clientMock);
        $this->assertEquals($expectedSecretEntity, $secret->getSecret('mysecretname'));
    }

    /**
     * All secrets should be returned with default maxResult
     */
    public function testGetSecrets(): void {
        $this->clientMock->method('get')->willReturn(json_decode(json_encode([
            'value' => [[
                'id' => 'https://kv-sdk-test.vault-int.azure-int.net/secrets/mysecretname/4387e9f3d6e14c459867679a90fd0f79',
                'attributes' => [
                    'enabled' => true,
                    'created' => 1493938410,
                    'updated' => 1493938410,
                    'recoveryLevel' => 'Recoverable+Purgeable',
                ], ],
            ],
            'nextLink' => 'https://myvault.vault.azure.net:443/secrets?api-version=7.1&$skiptoken=eyJOZXh0TWFya2VyIjoiMiE4OCFNREF3TURJeUlYTmxZM0psZEM5TVNWTlVVMFZEVWtWVVZFVlRWREVoTURBd01ESTRJVEl3TVRZdE1USXRNVGxVTWpNNk1UQTZORFV1T0RneE9ERXhNRm9oIiwiVGFyZ2V0TG9jYXRpb24iOjB9&maxresults=25',
        ])));

        $expectedIdRepository = new IdRepository();
        $expectedIdRepository->add($entity = new IdEntity(
            'https://kv-sdk-test.vault-int.azure-int.net/secrets/mysecretname/4387e9f3d6e14c459867679a90fd0f79',
            new SecretAttributeEntity(
                true,
                1493938410,
                1493938410,
                'Recoverable+Purgeable',
            ),
        ));
        $expectedIdRepository->setNextLink('https://myvault.vault.azure.net:443/secrets?api-version=7.1&$skiptoken=eyJOZXh0TWFya2VyIjoiMiE4OCFNREF3TURJeUlYTmxZM0psZEM5TVNWTlVVMFZEVWtWVVZFVlRWREVoTURBd01ESTRJVEl3TVRZdE1USXRNVGxVTWpNNk1UQTZORFV1T0RneE9ERXhNRm9oIiwiVGFyZ2V0TG9jYXRpb24iOjB9&maxresults=25');

        $secret = new Secret('https://kv-sdk-test.vault-int.azure-int.net', $this->clientMock);
        $this->assertEquals($expectedIdRepository, $secret->getSecrets());
    }

    /**
     * Given a vault link with nextLink all
     * secrets should be returned with default maxResult
     */
    public function testGetSecretsWithNextLink(): void {
        $this->clientMock->method('get')->willReturn(json_decode(json_encode([
            'value' => [[
                'id' => 'https://kv-sdk-test.vault-int.azure-int.net/secrets/mysecretname/4387e9f3d6e14c459867679a90fd0f79',
                'attributes' => [
                    'enabled' => true,
                    'created' => 1493938410,
                    'updated' => 1493938410,
                    'recoveryLevel' => 'Recoverable+Purgeable',
                ], ],
            ],
            'nextLink' => 'https://myvault.vault.azure.net:443/secrets?api-version=7.1&$skiptoken=eyJOZXh0TWFya2VyIjoiMiE4OCFNREF3TURJeUlYTmxZM0psZEM5TVNWTlVVMFZEVWtWVVZFVlRWREVoTURBd01ESTRJVEl3TVRZdE1USXRNVGxVTWpNNk1UQTZORFV1T0RneE9ERXhNRm9oIiwiVGFyZ2V0TG9jYXRpb24iOjB9&maxresults=25',
        ])));

        $expectedIdRepository = new IdRepository();
        $expectedIdRepository->add($entity = new IdEntity(
            'https://kv-sdk-test.vault-int.azure-int.net/secrets/mysecretname/4387e9f3d6e14c459867679a90fd0f79',
            new SecretAttributeEntity(
                true,
                1493938410,
                1493938410,
                'Recoverable+Purgeable',
            ),
        ));
        $expectedIdRepository->setNextLink('https://myvault.vault.azure.net:443/secrets?api-version=7.1&$skiptoken=eyJOZXh0TWFya2VyIjoiMiE4OCFNREF3TURJeUlYTmxZM0psZEM5TVNWTlVVMFZEVWtWVVZFVlRWREVoTURBd01ESTRJVEl3TVRZdE1USXRNVGxVTWpNNk1UQTZORFV1T0RneE9ERXhNRm9oIiwiVGFyZ2V0TG9jYXRpb24iOjB9&maxresults=25');

        $secret = new Secret('https://kv-sdk-test.vault-int.azure-int.net', $this->clientMock);
        $this->assertEquals($expectedIdRepository, $secret->getSecrets('https://my-keyvault-dns.vault.azure.net/secrets'));
    }

    /**
     * Set secret by
     * explicitly specifying name and value
     */
    public function testSetSecretByNameAndValue(): void {
        $this->clientMock->method('post')->willReturn(json_decode(json_encode([
            'value' => 'mysecretvalue',
            'id' => 'https://kv-sdk-test.vault-int.azure-int.net/secrets/mysecretname/4387e9f3d6e14c459867679a90fd0f79',
            'attributes' => [
                'enabled' => true,
                'created' => 1493938410,
                'updated' => 1493938410,
                'recoveryLevel' => 'Recoverable+Purgeable',
            ],
        ])));

        $expectedSecretEntity = new SecretEntity(
            'mysecretname',
            '4387e9f3d6e14c459867679a90fd0f79',
            'mysecretvalue',
            'https://kv-sdk-test.vault-int.azure-int.net/secrets/mysecretname/4387e9f3d6e14c459867679a90fd0f79',
            new SecretAttributeEntity(
                true,
                1493938410,
                1493938410,
                'Recoverable+Purgeable',
            )
        );

        $secret = new Secret('https://kv-sdk-test.vault-int.azure-int.net', $this->clientMock);
        $this->assertEquals($expectedSecretEntity, $secret->setSecret('mysecretname', 'mysecretvalue'));
    }
}
