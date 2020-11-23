<?php

namespace AzKeyVault\Tests;

use AzKeyVault\Key;
use AzKeyVault\Client;
use PHPUnit\Framework\TestCase;
use AzKeyVault\Responses\Key\KeyEntity;
use AzKeyVault\Responses\Key\KeyVersionEntity;
use AzKeyVault\Responses\Key\KeyAttributeEntity;
use AzKeyVault\Responses\Key\KeyVersionRepository;

class KeyTest extends TestCase {
    protected $clientMock;

    protected function setUp(): void {
        $this->clientMock = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * A key should be available by
     * explicitly specifing name and version
     */
    public function testGetKeyByNameAndVersion(): void {
        $this->clientMock->method('get')->willReturn(json_decode(json_encode([
            'key' => [
                'kid' => 'https://kv-sdk-test.vault-int.azure-int.net/keys/mykeyname/4387e9f3d6e14c459867679a90fd0f79',
                'kty' => 'RSA',
                'key_ops' => [
                    'encrypt', 'decrypt', 'sign',
                ],
                'n' => '2HJAE5fU3Cw2Rt9hEuq-F6XjINKGa-zskfISVqopqUy60GOs2eyhxbWbJBeUXNor_gf-tXtNeuqeBgitLeVa640UDvnEjYTKWjCniTxZRaU7ewY8BfTSk-7KxoDdLsPSpX_MX4rwlAx-_1UGk5t4sQgTbm9T6Fm2oqFd37dsz5-Gj27UP2GTAShfJPFD7MqU_zIgOI0pfqsbNL5xTQVM29K6rX4jSPtylZV3uWJtkoQIQnrIHhk1d0SC0KwlBV3V7R_LVYjiXLyIXsFzSNYgQ68ZjAwt8iL7I8Osa-ehQLM13DVvLASaf7Jnu3sC3CWl3Gyirgded6cfMmswJzY87w',
                'e' => 'AQAB',
            ],
            'attributes' => [
                'enabled' => true,
                'created' => 1493938410,
                'updated' => 1493938410,
                'recoveryLevel' => 'Recoverable+Purgeable',
            ],
        ])));

        $expectedKeyEntity = new KeyEntity(
            'mykeyname',
            '4387e9f3d6e14c459867679a90fd0f79',
            'https://kv-sdk-test.vault-int.azure-int.net/keys/mykeyname/4387e9f3d6e14c459867679a90fd0f79',
            'RSA',
            ['encrypt', 'decrypt', 'sign'],
            new KeyAttributeEntity(
                true,
                1493938410,
                1493938410,
                'Recoverable+Purgeable',
            ),
            null, null, null, 'AQAB',
            '2HJAE5fU3Cw2Rt9hEuq-F6XjINKGa-zskfISVqopqUy60GOs2eyhxbWbJBeUXNor_gf-tXtNeuqeBgitLeVa640UDvnEjYTKWjCniTxZRaU7ewY8BfTSk-7KxoDdLsPSpX_MX4rwlAx-_1UGk5t4sQgTbm9T6Fm2oqFd37dsz5-Gj27UP2GTAShfJPFD7MqU_zIgOI0pfqsbNL5xTQVM29K6rX4jSPtylZV3uWJtkoQIQnrIHhk1d0SC0KwlBV3V7R_LVYjiXLyIXsFzSNYgQ68ZjAwt8iL7I8Osa-ehQLM13DVvLASaf7Jnu3sC3CWl3Gyirgded6cfMmswJzY87w',
        );

        $key = new Key('https://kv-sdk-test.vault-int.azure-int.net', $this->clientMock);
        $this->assertEquals($expectedKeyEntity, $key->getKey('mykeyname', '4387e9f3d6e14c459867679a90fd0f79'));
    }

    /**
     * Given a key name all
     * versions should be returned
     */
    public function testGetKeyVersions() {
        $this->clientMock->method('get')->willReturn(json_decode(json_encode([
            'value' => [[
                'kid' => 'https://kv-sdk-test.vault-int.azure-int.net/keys/mykeyname/4387e9f3d6e14c459867679a90fd0f79',
                'attributes' => [
                    'enabled' => true,
                    'created' => 1493938410,
                    'updated' => 1493938410,
                    'recoveryLevel' => 'Recoverable+Purgeable',
                ],
            ]],
        ])));

        $expectedKeyVersionRepository = new KeyVersionRepository();
        $expectedKeyVersionRepository->add($entity = new KeyVersionEntity(
            'mykeyname',
            '4387e9f3d6e14c459867679a90fd0f79',
            'https://kv-sdk-test.vault-int.azure-int.net/keys/mykeyname/4387e9f3d6e14c459867679a90fd0f79',
            new KeyAttributeEntity(
                true,
                1493938410,
                1493938410,
                'Recoverable+Purgeable',
            )
        ));

        $key = new Key('https://kv-sdk-test.vault-int.azure-int.net', $this->clientMock);
        $this->assertEquals($expectedKeyVersionRepository, $key->getKeyVersions('mykeyname'));

        return $entity;
    }

    /**
     * A key should be returned by
     * passing an instance of KeyVersionEntity
     * @depends testGetKeyVersions
     * @param $versionReference
     */
    public function testGetKeyByVersionReference($versionReference): void {
        $this->clientMock->method('get')->willReturn(json_decode(json_encode([
            'key' => [
                'kid' => 'https://kv-sdk-test.vault-int.azure-int.net/keys/mykeyname/4387e9f3d6e14c459867679a90fd0f79',
                'kty' => 'RSA',
                'key_ops' => [
                    'encrypt', 'decrypt', 'sign',
                ],
                'n' => '2HJAE5fU3Cw2Rt9hEuq-F6XjINKGa-zskfISVqopqUy60GOs2eyhxbWbJBeUXNor_gf-tXtNeuqeBgitLeVa640UDvnEjYTKWjCniTxZRaU7ewY8BfTSk-7KxoDdLsPSpX_MX4rwlAx-_1UGk5t4sQgTbm9T6Fm2oqFd37dsz5-Gj27UP2GTAShfJPFD7MqU_zIgOI0pfqsbNL5xTQVM29K6rX4jSPtylZV3uWJtkoQIQnrIHhk1d0SC0KwlBV3V7R_LVYjiXLyIXsFzSNYgQ68ZjAwt8iL7I8Osa-ehQLM13DVvLASaf7Jnu3sC3CWl3Gyirgded6cfMmswJzY87w',
                'e' => 'AQAB',
            ],
            'attributes' => [
                'enabled' => true,
                'created' => 1493938410,
                'updated' => 1493938410,
                'recoveryLevel' => 'Recoverable+Purgeable',
            ],
        ])));

        $expectedKeyEntity = new KeyEntity(
            'mykeyname',
            '4387e9f3d6e14c459867679a90fd0f79',
            'https://kv-sdk-test.vault-int.azure-int.net/keys/mykeyname/4387e9f3d6e14c459867679a90fd0f79',
            'RSA',
            ['encrypt', 'decrypt', 'sign'],
            new KeyAttributeEntity(
                true,
                1493938410,
                1493938410,
                'Recoverable+Purgeable',
            ),
            null, null, null, 'AQAB',
            '2HJAE5fU3Cw2Rt9hEuq-F6XjINKGa-zskfISVqopqUy60GOs2eyhxbWbJBeUXNor_gf-tXtNeuqeBgitLeVa640UDvnEjYTKWjCniTxZRaU7ewY8BfTSk-7KxoDdLsPSpX_MX4rwlAx-_1UGk5t4sQgTbm9T6Fm2oqFd37dsz5-Gj27UP2GTAShfJPFD7MqU_zIgOI0pfqsbNL5xTQVM29K6rX4jSPtylZV3uWJtkoQIQnrIHhk1d0SC0KwlBV3V7R_LVYjiXLyIXsFzSNYgQ68ZjAwt8iL7I8Osa-ehQLM13DVvLASaf7Jnu3sC3CWl3Gyirgded6cfMmswJzY87w',
        );

        $key = new Key('https://kv-sdk-test.vault-int.azure-int.net', $this->clientMock);
        $this->assertEquals($expectedKeyEntity, $key->getKey($versionReference));
    }
}
