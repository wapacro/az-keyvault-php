<?php


namespace AzKeyVault\Tests;

use AzKeyVault\KeyUtil;
use PHPUnit\Framework\TestCase;
use AzKeyVault\Responses\Key\KeyEntity;
use AzKeyVault\Responses\Key\KeyAttributeEntity;

class KeyUtilTest extends TestCase {
    /**
     * Converting a public EC key from
     * JWK to PEM should be the same as
     * PEM generated using OpenSSL
     */
    public function testPrivateEcJwkToPem(): void {
        $keyResource = openssl_pkey_new([
            'private_key_type' => \OPENSSL_KEYTYPE_EC,
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
            $keyDetails['ec']['curve_name'],
            $keyDetails['ec']['x'], $keyDetails['ec']['y'],
        ));

        $this->assertEquals(
            $this->normalizeCryptoString($keyDetails['key']),
            $this->normalizeCryptoString($keyUtil->toPEM())
        );
    }

    /**
     * Converting a public RSA key from
     * JWK to PEM should be the same as
     * PEM generated using OpenSSL
     */
    public function testPrivateRsaJwkToPem(): void {
        $keyResource = openssl_pkey_new([
            'private_key_type' => \OPENSSL_KEYTYPE_RSA,
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
            null, null, null,
            $keyDetails['rsa']['e'], $keyDetails['rsa']['n']
        ));

        $this->assertEquals(
            $this->normalizeCryptoString($keyDetails['key']),
            $this->normalizeCryptoString($keyUtil->toPEM())
        );
    }

    protected function normalizeCryptoString(string $crypto) {
        // Replace CRLF with LF
        $crypto = preg_replace('~\r\n?~', "\n", $crypto);

        // Replace PKCS#1 with PKCS#8 headers
        return str_replace('RSA ', '', $crypto);
    }
}
