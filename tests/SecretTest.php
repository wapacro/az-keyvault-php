<?php

namespace AzKeyVault\Tests;

use AzKeyVault\Client;
use AzKeyVault\Responses\Secret\SecretAttributeEntity;
use AzKeyVault\Responses\Secret\SecretEntity;
use AzKeyVault\Responses\Secret\SecretVersionEntity;
use AzKeyVault\Responses\Secret\SecretVersionRepository;
use AzKeyVault\Secret;
use PHPUnit\Framework\TestCase;

class SecretTest extends TestCase {

	protected $clientMock;

	/**
	 * A secret should be available by
	 * explicitly specifying name and version
	 */
	public function testGetSecretByNameAndVersion() {
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
				]],
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
	public function testGetSecretByVersionReference($versionReference) {
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

	protected function setUp(): void {
		$this->clientMock = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
	}

}
