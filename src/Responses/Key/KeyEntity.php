<?php


namespace AzKeyVault\Responses\Key;

use AzKeyVault\Contracts\EntityInterface;
use AzKeyVault\Contracts\AttributeInterface;

class KeyEntity implements EntityInterface {
    /** @var string */
    public $name;

    /** @var string */
    public $version;

    /**
     * JsonWebKey Key Type (kty)
     * @var string
     *             Possible values: EC, EC-HSM, RSA, RSA-HSM, oct
     */
    public $type;

    /** @var string */
    public $url;

    /** @var array */
    public $operations;

    /**
     * Elliptic curve name
     * @var string
     *             Possible values: P-256, P-256K, P-384, P-521
     */
    public $crv;

    /**
     * X component of EC public keys
     * @var string
     */
    public $x;

    /**
     * Y component of EC public keys
     * @var string
     */
    public $y;

    /**
     * RSA public exponent
     * @var string
     */
    public $e;

    /**
     * RSA modulus
     * @var string
     */
    public $n;

    /** @var KeyAttributeEntity */
    public $attributes;

    public function __construct(string $name, string $version, string $url,
                                string $type, array $operations, KeyAttributeEntity $attributes,
                                string $crv = null, string $x = null, string $y = null,
                                string $e = null, string $n = null) {
        $this->name = $name;
        $this->version = $version;
        $this->url = $url;
        $this->type = $type;
        $this->operations = $operations;
        $this->crv = $crv;
        $this->x = $x;
        $this->y = $y;
        $this->e = $e;
        $this->n = $n;
        $this->attributes = $attributes;
    }

    public function __toString() {
        return $this->type;
    }

    public function getAttributes(): AttributeInterface {
        return $this->attributes;
    }
}
