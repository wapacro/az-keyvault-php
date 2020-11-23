<?php


namespace AzKeyVault;

use Jose\Component\Core\JWK;
use Jose\Component\Core\Util\ECKey;
use Jose\Component\Core\Util\RSAKey;
use AzKeyVault\Responses\Key\KeyEntity;

class KeyUtil {
    /** @var KeyEntity */
    protected $key;

    /** @var JWK */
    protected $jwk;

    /**
     * KeyUtil constructor
     * @param KeyEntity $key
     */
    public function __construct(KeyEntity $key) {
        $this->key = $key;
        $this->jwk = new JWK(array_filter([
            'kty' => $key->type,
            'crv' => $key->crv,
            'x' => $key->x,
            'y' => $key->y,
            'e' => $key->e,
            'n' => $key->n,
        ]));
    }

    /**
     * Returns the PEM represantation
     * of the key
     * @return string
     */
    public function toPEM() {
        if (!empty($this->key->crv)) {
            // EC Key
            return ECKey::convertToPEM($this->jwk);
        }

        // RSA Key
        return RSAKey::createFromJWK($this->jwk)->toPEM();
    }
}
