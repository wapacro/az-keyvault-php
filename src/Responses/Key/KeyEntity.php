<?php


namespace AzKeyVault\Responses\Key;


use AzKeyVault\Contracts\EntityInterface;

class KeyEntity implements EntityInterface {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $version;

	/**
	 * JsonWebKey Key Type (kty)
	 * @var string
	 * Possible values: EC, EC-HSM, RSA, RSA-HSM, oct
	 */
	public $type;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var string
	 */
	public $hsm;

	/**
	 * @var array
	 */
	public $operations;

	/**
	 * Elliptic curve name
	 * @var string
	 * Possible values: P-256, P-256K, P-384, P-521
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
	 * D component of EC private keys
	 * or RSA private exponent
	 * @var string
	 */
	public $d;

	/**
	 * RSA private key parameter
	 * @var string
	 */
	public $dp;

	/**
	 * RSA private key parameter
	 * @var string
	 */
	public $dq;

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

	/**
	 * RSA secret prime
	 * @var string
	 */
	public $p;

	/**
	 * RSA secret prime, with p < q
	 * @var string
	 */
	public $q;

	/**
	 * RSA private key parameter
	 * @var string
	 */
	public $qi;

	/**
	 * Symmetric key
	 * @var string
	 */
	public $k;

	/**
	 * @var KeyAttributeEntity
	 */
	public $attributes;

	public function __construct(string $name, string $version, string $url,
								string $type, array $operations, KeyAttributeEntity $attributes,
								string $hsm = null, string $crv = null, string $x = null,
								string $y = null, string $d = null, string $dp = null,
								string $dq = null, string $e = null, string $n = null,
								string $p = null, string $q = null, string $qi = null, string $k = null) {
		$this->name = $name;
		$this->version = $version;
		$this->url = $url;
		$this->type = $type;
		$this->operations = $operations;
		$this->hsm = $hsm;
		$this->crv = $crv;
		$this->x = $x;
		$this->y = $y;
		$this->d = $d;
		$this->dp = $dp;
		$this->dq = $dq;
		$this->e = $e;
		$this->n = $n;
		$this->p = $p;
		$this->q = $q;
		$this->qi = $qi;
		$this->k = $k;
		$this->attributes = $attributes;
	}

	public
	function __toString() {
		return ''; // todo: Return something useful
	}

}
