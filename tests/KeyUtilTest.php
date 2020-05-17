<?php


namespace AzKeyVault\Tests;


use AzKeyVault\KeyUtil;
use AzKeyVault\Responses\Key\KeyAttributeEntity;
use AzKeyVault\Responses\Key\KeyEntity;
use PHPUnit\Framework\TestCase;

class KeyUtilTest extends TestCase {

	/**
	 * Converting a private EC key from
	 * JWK to PEM should be the same as
	 * pregenerated PEM using OpenSSL
	 */
	public function testPrivateEcJwkToPem() {
		$keyResource = openssl_pkey_new([
			'private_key_type' => OPENSSL_KEYTYPE_EC,
			'curve_name' => 'prime256v1',
		]);

		openssl_pkey_export($keyResource, $privateKey);
		$keyDetails = openssl_pkey_get_details(openssl_pkey_get_private($privateKey));

		$keyDetails['ec'] = array_map(function ($element) {
			return base64_encode($element);
		}, $keyDetails['ec']);

		$keyDetails['ec']['curve_name'] = 'P-256';
		$keyUtil = new KeyUtil(new KeyEntity(
			'mykey',
			'1',
			'http://mykey',
			'EC',
			['none'],
			new KeyAttributeEntity(true, time(), time(), ''),
			null, $keyDetails['ec']['curve_name'],
			$keyDetails['ec']['x'], $keyDetails['ec']['y'],
			$keyDetails['ec']['d']
		));

		$this->assertEquals(
			$this->normalizeCryptoString($privateKey),
			$this->normalizeCryptoString($keyUtil->toPEM())
		);
	}

	protected function normalizeCryptoString(string $crypto) {
		// Replace CRLF with LF
		$crypto = preg_replace('~\r\n?~', "\n", $crypto);

		// Replace PKCS#1 with PKCS#8 headers
		$crypto = str_replace('RSA ', '', $crypto);

		return $crypto;
	}

	/**
	 * Generating a public EC key from
	 * private EC key JWK and converting
	 * it to PEM should be the same as
	 * pregenerated PEM using OpenSSL
	 */
	public function testPublicEcJwkToPem() {
		$keyResource = openssl_pkey_new([
			'private_key_type' => OPENSSL_KEYTYPE_EC,
			'curve_name' => 'prime256v1',
		]);

		openssl_pkey_export($keyResource, $privateKey);
		$keyDetails = openssl_pkey_get_details(openssl_pkey_get_private($privateKey));

		$keyDetails['ec'] = array_map(function ($element) {
			return base64_encode($element);
		}, $keyDetails['ec']);

		$keyDetails['ec']['curve_name'] = 'P-256';
		$keyUtil = new KeyUtil(new KeyEntity(
			'mykey',
			'1',
			'http://mykey',
			'EC',
			['none'],
			new KeyAttributeEntity(true, time(), time(), ''),
			null, $keyDetails['ec']['curve_name'],
			$keyDetails['ec']['x'], $keyDetails['ec']['y'],
			$keyDetails['ec']['d']
		));

		$this->assertEquals(
			$this->normalizeCryptoString($keyDetails['key']),
			$this->normalizeCryptoString($keyUtil->toPublicKey()->toPEM())
		);
	}

	/**
	 * Converting a private RSA key from
	 * JWK to PEM should be the same as
	 * pregenerated PEM using OpenSSL
	 */
	public function testPrivateRsaJwkToPem() {
		$keyResource = openssl_pkey_new([
			'private_key_type' => OPENSSL_KEYTYPE_RSA,
			'private_key_bits' => 2048,
		]);

		openssl_pkey_export($keyResource, $privateKey);
		$keyDetails = openssl_pkey_get_details(openssl_pkey_get_private($privateKey));
		$keyDetails['rsa'] = array_map(function ($element) {
			return base64_encode($element);
		}, $keyDetails['rsa']);


		$keyUtil = new KeyUtil(new KeyEntity(
			'mykey',
			'1',
			'http://mykey',
			'RSA',
			['none'],
			new KeyAttributeEntity(true, time(), time(), ''),
			null, null, null, null,
			$keyDetails['rsa']['d'], $keyDetails['rsa']['dmp1'],
			$keyDetails['rsa']['dmq1'], $keyDetails['rsa']['e'],
			$keyDetails['rsa']['n'], $keyDetails['rsa']['p'],
			$keyDetails['rsa']['q'], $keyDetails['rsa']['iqmp']
		));

		$this->assertEquals(
			$this->normalizeCryptoString($privateKey),
			$this->normalizeCryptoString($keyUtil->toPEM())
		);
	}

	/**
	 * Generating a public RSA key from
	 * private RSA key JWK and converting
	 * it to PEM should be the same as
	 * pregenerated PEM using OpenSSL
	 */
	public function testPublicRsaJwkToPem() {
		$keyResource = openssl_pkey_new([
			'private_key_type' => OPENSSL_KEYTYPE_RSA,
			'private_key_bits' => 2048,
		]);

		openssl_pkey_export($keyResource, $privateKey);
		$keyDetails = openssl_pkey_get_details(openssl_pkey_get_private($privateKey));

		$keyDetails['rsa'] = array_map(function ($element) {
			return base64_encode($element);
		}, $keyDetails['rsa']);

		$keyUtil = new KeyUtil(new KeyEntity(
			'mykey',
			'1',
			'http://mykey',
			'RSA',
			['none'],
			new KeyAttributeEntity(true, time(), time(), ''),
			null, null, null, null,
			$keyDetails['rsa']['d'], $keyDetails['rsa']['dmp1'],
			$keyDetails['rsa']['dmq1'], $keyDetails['rsa']['e'],
			$keyDetails['rsa']['n'], $keyDetails['rsa']['p'],
			$keyDetails['rsa']['q'], $keyDetails['rsa']['iqmp']
		));

		$this->assertEquals(
			$this->normalizeCryptoString($keyDetails['key']),
			$this->normalizeCryptoString($keyUtil->toPublicKey()->toPEM())
		);
	}

}
